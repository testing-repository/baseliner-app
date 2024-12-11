<?php

class Courier {
    private $apiUrl = "https://mtapi.net/?testMode=1";
    private $apiKey = "f16753b55cac6c6e";

    /**
     * Creates a new shipment package and returns the tracking number.
     *
     * @param array $order The order details including sender, receiver, and products.
     * @param array $params Additional parameters for the shipment.
     * @return string The tracking number of the shipment.
     * @throws Exception If there is an error with the API or address handling.
     */
    public function newPackage(array $order, array $params): string {
        // Handle address character limits for the receiver
        $this->addressHandler($order['receiver'], $params);

        $postData = [
            'Apikey' => $this->apiKey,
            'Command' => 'OrderShipment',
            'Shipment' => array_merge($params, [
                'ConsignorAddress' => $order['sender'],
                'ConsigneeAddress' => $order['receiver'],
                'Products' => $order['products'],
            ]),
        ];

        $response = $this->sendApiRequest($postData);

        if (isset($response['ErrorLevel']) && $response['ErrorLevel'] !== 0) {
            $this->outputError("API Error: " . ($response['Error'] ?? "Unknown error"));
        }

        return $response['Shipment']['TrackingNumber'] ?? $this->outputError("Tracking number not received.");
    }

    /**
     * Generates and outputs the shipment label as a PDF file.
     *
     * @param string $trackingNumber The tracking number of the shipment.
     * @throws Exception If the label cannot be retrieved or decoded.
     */
    public function packagePDF(string $trackingNumber): void {
        $postData = [
            "Apikey" => $this->apiKey,
            "Command" => "GetShipmentLabel",
            "Shipment" => [
                "LabelFormat" => "PDF",
                "TrackingNumber" => $trackingNumber,
            ],
        ];

        $response = $this->sendApiRequest($postData);

        if (isset($response['ErrorLevel']) && $response['ErrorLevel'] !== 0) {
            $this->outputError("API Error: " . ($response['Error'] ?? "Unknown error"));
        }

        if (isset($response['Shipment']['LabelImage'])) {
            $labelPdf = base64_decode($response['Shipment']['LabelImage']);
            if (empty($labelPdf)) {
                throw new Exception("Failed to decode the label image or the label is empty.");
            }

            $this->outputPdf($labelPdf);
        } else {
            $this->outputError("Label not found in the response.");
        }

    }

    /**
     * Handles address splitting based on character limits.
     *
     * @param array $receiver The receiver address details.
     * @param array $params Additional parameters to determine service limits.
     * @throws Exception If the address exceeds the allowed character limits.
     */
    private function addressHandler(array &$receiver, array $params): void {
        $limits = $this->getServiceLimits($params['Service'] ?? null);
    
        if (!$limits || empty($receiver['AddressLine1'])) {
            return;
        }
    
        $splitedAddress = $this->splitAddress($receiver['AddressLine1'], $limits);
    
        if (!empty($splitedAddress['overflow'])) {
            throw new Exception("Address too long for the selected service ({$params['Service']}).");
        }
    
        $this->updateReceiverAddress($receiver, $splitedAddress);
    }

    /**
     * Retrieves the character limits for a given service.
     *
     * @param string|null $service The service type.
     * @return array|null The character limits for the service, or null if not defined.
     */
    private function getServiceLimits(?string $service): ?array {
        $defaultLimits = ['AddressLine1' => 35, 'AddressLine2' => 35, 'AddressLine3' => 35];
        $serviceLimits = [
            'PPLEU' => $defaultLimits,
            'PPLGE/GU' => ['AddressLine1' => 50, 'AddressLine2' => 50, 'AddressLine3' => 50],
            'RM24/48(S)' => ['AddressLine1' => 30, 'AddressLine2' => 30, 'AddressLine3' => 30],
            'PPTT' => ['AddressLine1' => 30, 'AddressLine2' => 30, 'AddressLine3' => 30],
            'PPTR/NT' => ['AddressLine1' => 30, 'AddressLine2' => 30, 'AddressLine3' => 30],
            'SEND(2)' => $defaultLimits,
            'ITCR' => ['AddressLine1' => 60, 'AddressLine2' => 60, 'AddressLine3' => 60],
            'HEHDS' => $defaultLimits,
            'CPHD(S)' => $defaultLimits,
            'SC***' => $defaultLimits,
            'PPND/HD(S)' => $defaultLimits,
        ];
    
        return $serviceLimits[$service] ?? null;
    }

    /**
     * Splits an address into lines based on character limits.
     *
     * @param string $fullAddress The full address as a single string.
     * @param array $limits The character limits for each address line.
     * @return array The split address lines and any overflow.
     */
    private function splitAddress(string $fullAddress, array $limits): array {
        $result = [];
        $remaining = $fullAddress;
    
        foreach (['AddressLine1', 'AddressLine2', 'AddressLine3'] as $address) {
            $limit = $limits[$address] ?? 0;
            $result[$address] = substr($remaining, 0, $limit);
            $remaining = substr($remaining, $limit) ?: '';

            if (empty($result[$address])) {
                unset($result[$address]);
            }
        }

        if (!empty($remaining)) {
            $result['overflow'] = $remaining;
        }
    
        return $result;
    }

    /**
     * Updates the receiver address with the split address lines.
     *
     * @param array $receiver The receiver address details.
     * @param array $splitedAddress The split address lines.
     */
    private function updateReceiverAddress(array &$receiver, array $splitedAddress): void {
        
        foreach (['AddressLine1', 'AddressLine2', 'AddressLine3'] as $address) {           
            if (!empty($splitedAddress[$address])) {
                $receiver[$address] = $splitedAddress[$address];
            } else {
                unset($receiver[$address]);
            }
        }
    }

    /**
     * Sends an API request and returns the response.
     *
     * @param array $postData The data to be sent to the API.
     * @return array The decoded API response.
     */
    private function sendApiRequest(array $postData): array {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/json',
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->outputError("Connection error: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    /**
     * Outputs a PDF file to the browser.
     *
     * @param string $labelPdf The PDF content to output.
     */
    private function outputPdf(string $labelPdf): void {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="shipment_label.pdf"'); 
        
        echo $labelPdf;
        exit;
    }

    /**
     * Outputs an error message and stops execution.
     *
     * @param string $message The error message to display.
     */
    private function outputError(string $message): void {
        echo "<h3>$message</h3>";
        exit;
    }
}
