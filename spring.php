<?php
require_once 'Courier.php';


// Order data
$order = [
    'sender' => [
        'Company' => 'BaseLinker',
        'Name' => 'Jan Kowalski',
        'AddressLine1' => 'Kopernika 10',
        'City' => 'Gdansk',
        'Zip' => '80208',
        'Email' => '',
        'Phone' => '123456789',
    ],
    'receiver' => [
        'Company' => 'Spring GDS',
        'Name' => 'Maud Driant',
        'AddressLine1' => 'Strada Foisorului, Nr. 16, Bl. F11C, Sc. 1, Ap. 10',
        'City' => 'Bucuresti, Sector 3',
        'Zip' => '031179',
        'Country' => 'RO',
        'Email' => 'john@doe.com',
        'Phone' => '555555555',
    ],
    'products' => [
        [
            'Description' => 'CD: The Postal Service - Give Up',
            'HsCode' => '852349',
            'Quantity' => 2,
            'Value' => 20,
            'Weight' => 0.8,
        ],
    ],
];

// API parameters
$params = [
    'ShipperReference' => uniqid('REF_', true),
    'Service' => 'PPTT',
    'Weight' => 1,
    'Value' => 120,
    'LabelFormat' => 'PDF',
];

// Creating the courier object
$courier = new Courier();

try {
    // Creating the shipment using the provided order and API parameters
    // The function returns the shipment's tracking number
    $trackingNumber = $courier->newPackage($order, $params);

    // Retrieving and displaying the label
    $courier->packagePDF($trackingNumber);
} catch (Exception $e) {
    echo "<div style='border: 2px solid #ff4d4d; background-color: #ffe6e6; padding: 15px; border-radius: 5px; font-family: Arial, sans-serif;'>
        <h3 style='color: #b30000;'>Error Encountered</h3>
        <p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p style='color: #666;'>Please review the provided data and try again. If the issue persists, contact support.</p>
    </div>";
}
