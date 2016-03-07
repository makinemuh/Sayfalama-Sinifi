# Sayfalama Sınıfı
Sayfalama sınıfı ile yüksek adetli verileri sayfalayın

## Kullanımı
```php
$question = new Question;
$count = $question->fetch('SELECT COUNT(id) as count FROM question');
$pagination = new Pagination($count->count, 10, 1, 'http://site.com/?sayfa=(:num)');
$questions = $question->getAll(['limit' => $pagination->limit()]);
$pages = $pagnation->toHtml();
```
Açıklamalı haliyle yazalım:
```php
$pagination = new Pagination($toplamOgeSayisi, $sayfaBasinaOgeSayisi, $gecerliSayfa, $sayfaURL);
```

## Metod Listesi
- `setMaxPages(int $maxPages)` Bir satırda gösterilecek sayfa sayısını belirler
- `setCurPage(int $curPage)` Mevcut bulunulan sayfayı belirler
- `setTotalItems(int $totalItems)` Toplam öğe sayısını beliriler
- `setPattern(string $pattern)` URL desenini belirler
- `getPageUrl(int $pageNum)` Sayfa sayısına göre URL döndürür
- `getNextPage()` Sonraki sayfa numarasını döndürür
- `getPrevPage()` Bir önceki sayfa numarasını döndürür
- `getNextUrl()` Bir sonraki sayfa URL'sini döndürür
- `getPrevUrl()` Bir önceki sayfa URL'sini döndürür
- `getPages()` Sayfaları oluşturur
- `toHtml(bool $pager = false)` Sayfaları HTML biçiminde döndürür
- `limit()` SQL Sorgusu için limit sayısı döndürür

## Farklı senaryo
Basit bir örnekle, bir array sayfalayalım
```php
$arr = [
  'Gönderi 1',
  'Gönderi 1',
  'Gönderi 1',
  'Gönderi 1',
  'Gönderi 1',
  'Gönderi 1',
];
```
