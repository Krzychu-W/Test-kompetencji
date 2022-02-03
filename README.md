# Test kompetencji php

Aplikacja napisane w PhpStorm.

Aplikacja dzieli się na dwie części:

- framework qAnt
- docelową klasę Dziedzina

## Framework qAnt

Znajduje się w katalogu core. Jest to mój prywatny framework, który buduje od 2 lat z przeznaczeniem do własnych celów.
Zrobiłem go z powodu przewidywanego końca Drupal'a 7.

Moje własne serwisy są oparte na Drupal'u 7

- http://wp39.struktury.net/
- http://kumuradojo.pl/

Jego następca, Drupal 8/9 jest pomyłką, która wypacza ideę powszechnego CMS'a.

W oparciu na qAnt zbudowałem aplikację phpBook, której założeniem jest szybkość działania i wielodomenowość.

Nie jest jeszcze ukończona, ale jedna strona już powstała, mam też wieli mikro stron.

- http://grawitacja.struktury.net/
- http://historia.struktury.net/

qAnt nie jest oparty na namespace i nie zawiera obsługi mySql. Opiera się na moich **doświadczeniach** z:

- Zend 1.5
- CMS Simple
- Drupal 7
- I-Sklep
- i przede wszystkim autorskiego framework'u Clouder używanego w firmie Investmag od 10 lat, którego jestem pomysłodawcą i współautorem.  

Obsługę mySql dodałem w formie szczątkowej do qAnt, ale nie ma tam wyrafinowanych metod.

Z tej wersji qAnt usunąłem to, co uznałem za nadmiarowe.
Zostały tam elementy zbędne, których usuwanie wymagałoby dodatkowego czasy.

qAnt jest pisany dla **PHP 7.0** a nie dla **PHP 7.4**

Najstarsze fragmenty qAnt mogę sięgać PHP 5 (dziedziczone fragmenty kodu ze staroci).

## Instalacja

Aplikacja działa z bazą danych mySql. Podłączenie do bazy ustawia się w pliku:
- /config/sql.php

Nazwy zmiennych są intuicyjne. Bazą należy załadować plikiem:

- /config/install.sql

Wersja działająca jest pod adresem http://test.struktury.net/ 

## Klasa Dziedzina

Znajduje się w katalogu extra/Alteris i jest pisana w namespace dla PHP 7.4 pisana na XAMPP 7.4 dla Windows.

### Model danych

Model danych opiera się na trzech klasach

- Alteris\Model\Table
- Alteris\Model\Record
- Alteris\Model\Form

#### Table

Tabela modelu ma za zadanie definicję pól i reguł oraz komunikację z bazą SQL.
W niej znajdują się wszelkie zapytania do bazy i jest klasą nadrzędną do
pobierania i zapisy rekordów. Uproszczony model danych ogranicza się do identyfikacji tabel
poprzez pole id z autoincrement.

#### Record

Rekord modelu zaprojektowany jest jako bardzo lekki. Ma nie obciążać pamięci i dlatego 
pozbawiony jest walidacji danych przed zapisem. W nim mają być dostępne metody obiektu.

#### Form

Formularz modelu służy o administracji obiektu, W nim znajdują się walidacja pól i formularze edycji 
Pozwala to na odciążenie frontu.

W wypadku chęci walidacji rekordu przed zapisem można zastosować serię poleceń

```php
<?php
$record = $table->newRecord();
$record->name = ...;
$form = new Form($record);
if ($form->validate() {
  $record->save();
}
?>
```

Walidacja pól odbywa się metodami definiowanymi poprzez nazwę:

- field<Nazwa pola dużą literą>Validate

Nie wyklucza to jednak dodanie do obiektu Record dodatkowej walidacji. Ze względu na szybkość działania do tej walidacji
preferuję zabezpieczenia w bazie SQL (unique, klucze obce) i obsługę błędów zapisu. 

## Jednostki miar

Kontroler: qUnitController
Klasy:
- Alteris\Unit\Table
- Alteris\Unit\Record
- Alteris\Unit\Form

Dodałem założenie, że nazwa jednostki miary nie może się powtórzyć.
Pozwala to na zobrazowanie walidacji danych. Dodatkowo walidacja obejmuje usuwanie rekordów poprzez
\Alteris\Unit\Table::delete().  

## Materiały

Kontroler: qProductController
Klasy:
- Alteris\Product\Table
- Alteris\Product\Record
- Alteris\Product\Form

Uproszczony model zakłada połączenie produktu z liściem w jednej tabeli, co nie jest rozwiązaniem optymalnym (brak tabeli relacji product-group),
ale przyspiesza napisanie testu. 

## Grupa materiałów

Kontroler: qGroupController
Klasy:
- Alteris\Group\Table
- Alteris\Group\Record
- Alteris\Group\Form
- Alteris\Group\Tree

Stanąłem tutaj przed wyborem sposobu zapisu hierarchii. Istnieje wiele możliwości zależnie od ilości danych.
Zdecydowałem się na model minimalizujący czas odczytu na stronie internetowej. Przyjęty model danych w tabeli SQL 
wymaga zdefiniowania maksymalnego zagłębienia. Zagłębienie to może być zmieniane konfiguracji i jego przestawienie
wymaga zaktualizowania struktury.

Korzeń nie istnieje w zapisie SQL. Jest rekordem wirtualnym z id = 0

Z diagramu UML odczytałem, że produktu mogą być tylko w liściach i tak zaimplementowałem walidację.

Obiekt Tree służy prezentacji zagnieżdżonego drzewa. Obiekt prezentowany jest w nowym oknie w postaci dump'u.
Można rozwinąć od dowolnej grupy. 

## Klasy kontra tablice

W elementach wewnętrznych preferuję tablice, ponieważ są one znacznie szybsze i lżejsze niź klasy. Klasy rezerwuję do
elementów wystawianych na zewnątrz. 

## Test jednostkowe

Ogólną zasadę testów zrozumiałem. Jednak za późno już, żeby próbować je implementować. Na pewno nie wiem jak preparować
bazę danych do nich i na jakiej zasadzie budować strukturę plików celem automatyzacji.

**Pozdrawiam, Krzysztof Wałek.**
