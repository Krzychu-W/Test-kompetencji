# Test kompetencji php

Aplikacja dzieli się na dwie części:

- framework qAnt
- docelową klasę Dziedzina

## Framework qAnt

Znajduje się w katalogu core. Jest to mój prywatny framework, który buduje od 2 lat z przeznaczeniem do własnych celów.
Zrobiłem go z powodu przewidywanego końca Drupal'a 7.

Moje własne serwisy są oparte na Drupal'u 7

- http://wp39.struktury.net/
- http://kumuradojo.pl/

Jego następca, Drupal 8/9 jest pomyłką, która wypacza ideę powszechnego CRM'a.

W oparciu na qAnt zbudowałem aplikację phpBook, której założeniem jest szybkość działania i wielodomenowość.

Nie jest jeszcze ukończona, ale jedna strona już powstała:

- http://grawitacja.struktury.net/

qAnt nie jest oparty na namespace i nie zawiera obsługi mySql. Opiera się na moich doświadczeniach z:

- Zend 1.5
- CMS Simple
- Drupal 7
- I-Sklep
- i przedw wszystkim autorskiego framework'u Clouder używanego w firmie Investmag od 10 lat, którego jestem pomysłodawcą
- i współautorem.  

Obsługę mySql dodałem w formie szczątkowej do qAnt, ale nie ma tam wyrafinowanych metod.

Z dodanej wersji qAnt usunąłem to, co uznałem za nadmiarowe.
Mogły tam pozostać elementy, które 

## Klasa Dziedzina

Znajduje się w katalogu extra/Alteris i jest pisana w namespace.

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

Rekord modelu zaprojektowany jes jako bardzo lekki. Ma nie obciążać pamieci i dlatego 
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

## Grupa materiałów

Kontroler: qGroupController
Klasy:
- Alteris\Group\Table
- Alteris\Group\Record
- Alteris\Group\Form

Stanąłem tutaj przed wyborem sposoby zapisu hierarchii. Istnieje wiele możliwości
zależnie od ilości danych. Zdecydowałem się na model minimalizujący czas odczytu 
na stronie internetowej. Przyjęty model danych w tabeli SQL wymaga zdefiniowania maksymalnego zagłębienia. Zagłębienie to 
może być zmieniane konfiguracji i jego przestawienie wymaga zaktualizowania struktury.

Korzeń nie istnieje w zapisie SQL. Jest rekordem wirtualnym z id = 0



