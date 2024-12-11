# Projekt rekrutacyjny do firmy Baselinker

**Witam serdecznie starałem się wykonać wszystkie zadania, które zostały określone w tym projekcie. Wierzę, że zrealizowałem wszystkie założenia, chyba że niezamierzenie mogłem coś źle zrozumieć. Poniżej przedstawiam główne założenia projektu oraz opis, w jaki sposób zostały one zaimplementowane:**

### Wymagania projektu i moje wykonanie:

1. **Cel projektu**:  
   - Celem było utworzenie przesyłki oraz pobranie etykiety przewozowej dla brokera kurierskiego Spring.  
   - **Wykonanie**: Funkcja `newPackage()` została zaprojektowana do tworzenia przesyłki, a funkcja `packagePDF()` do pobierania etykiety przewozowej.

2. **Plik bazowy**:  
   - Plik `spring.php` został wskazany jako plik bazowy, zawierający podstawowe parametry przesyłki oraz dane nadawcy i odbiorcy.  
   - **Wykonanie**: Plik `spring.php` wykorzystałem jako główny skrypt testowy dla klasy `Courier.php`.

3. **Funkcja `newPackage()`**:  
   - **Założenia**:  
     - Przyjmuje parametry `array $order` (dane adresowe) oraz `array $params` (API key, usługa, itp.).  
     - W przypadku niepowodzenia wyświetla czytelny błąd zwrócony przez API.  
   - **Wykonanie**:  
     - Funkcja `newPackage()` w klasie `Courier.php` obsługuje dane `order` i `params`, tworzy przesyłkę i zwraca numer przesyłki. Dodatkowo zaimplementowałem obsługę błędów API, aby wyświetlać czytelne komunikaty. Mam nadzieję, że funkcja spełnia założenia.

4. **Funkcja `packagePDF()`**:  
   - **Założenia**:  
     - Przyjmuje `string $trackingNumber` (numer przesyłki zwrócony przez `newPackage()`).  
     - Wyświetla czytelny błąd w przypadku niepowodzenia lub etykietę do pobrania.  
   - **Wykonanie**:  
     - Funkcja `packagePDF()` zwraca etykietę przewozową w formacie możliwym do pobrania i obsługuje błędy API. Starałem się, aby była zgodna z założeniami i działała poprawnie.

5. **Obsługa błędów**:  
   - **Założenia**:  
     - Kod miał obsługiwać błędy zwracane przez API (np. błędy połączenia, błędne dane API) i zwracać czytelne komunikaty w przeglądarce.  
   - **Wykonanie**:  
     - Starałem się zaimplementować obsługę błędów w sposób czytelny i zrozumiały dla użytkownika, wyświetlając odpowiednie komunikaty w przeglądarce.

6. **Zgodność z zasadą KISS**:  
   - **Założenia**:  
     - Kod miał być prosty i przejrzysty, podzielony na jak najmniejszą liczbę plików.  
   - **Wykonanie**:  
     - Postarałem się podzielić projekt na dwa pliki: `Courier.php` (logika przesyłek) oraz `spring.php` (skrypt bazowy), zgodnie z założeniami zasady KISS.

7. **Limity znaków w adresach**:  
   - **Założenia**:  
     - Należało obsłużyć limity znaków w polach adresowych.  
   - **Wykonanie**:  
     - Zaimplementowałem mechanizm obsługi limitów znaków w polach adresowych i starałem się, aby działał poprawnie. Wierzę, że spełniłem to wymaganie.

8. **Standard kodowania PSR-12**:  
   - **Założenia**:  
     - Kod miał być zgodny ze standardem PSR-12.  
   - **Wykonanie**:  
     - Starałem się, aby kod został sformatowany zgodnie z wytycznymi PSR-12. Wierzę, że spełniłem wymagania, choć nie wykluczam, że mógł mi umknąć jakiś drobny szczegół.
    
**Przez cały czas pracy nad kodem korzystałem z testowej wersji API. Projekt, który przesłałem do tego repozytorium, również został skonfigurowany w klasie Courier.php tak, aby łączył się z testowym API.**

## Opis

Jest to projekt napisany w języku PHP, składający się z dwóch plików:

- **Courier.php** - Główna klasa projektu, zawiera logikę dotyczącą przesyłek.
- **spring.php** - Plik główny, który uruchamia projekt i testuje funkcjonalności.

## Wymagania

- PHP w wersji 7.0 lub wyższej.
- Serwer lokalny (np. XAMPP, WAMP, MAMP, itp.).

## Instrukcja uruchamiania

Aby uruchomić projekt lokalnie, wykonaj następujące kroki:

1. **Pobierz projekt**:
   - Możesz pobrać projekt z GitHub lub sklonować repozytorium za pomocą komendy:
     ```bash
     git clone https://github.com/testing-repository/baseliner-app.git
     ```

2. **Przenieś projekt do folderu htdocs**:
   - Jeśli używasz XAMPP, skopiuj pliki` do folderu `htdocs`, który znajduje się w katalogu instalacyjnym XAMPP.

3. **Uruchom serwer lokalny**:
   - Uruchom XAMPP (lub inny serwer lokalny), włącz Apache.

4. **Otwórz przeglądarkę**:
   - Wejdź na stronę projektu w przeglądarce, wpisując:
     ```
     http://localhost/spring.php
     ```

5. **Testowanie**:
   - Po załadowaniu strony, kod w `spring.php` uruchomi logikę zawartą w `Courier.php`. 
