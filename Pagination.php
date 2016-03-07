<?php

/**
 * Sayfalama Sınıfı
 * Verileri sayfalama sınıfı
 * @author Yılmaz Demir <demiriy@gmail.com>
 * @link http://demirphp.com
 * @package Pagination
 * @version 1.0
 */

class Pagination
{
	/**
	 * Numara tutucu
	 * @var string
	 */
	public $placeholder = '(:num)';

	/**
	 * Toplam öğe sayısı
	 * @var integer
	 */
	public $totalItems;

	/**
	 * Toplam sayfa sayısı
	 * @var integer
	 */
	public $totalPages;

	/**
	 * Sayfa başına öğe sayısı
	 * @var integer
	 */
	public $perPage;

	/**
	 * Geçerli sayfa
	 * @var integer
	 */
	public $curPage;

	/**
	 * Bağlantı kalıbı
	 * @var string
	 */
	public $pattern;

	/**
	 * En fazla gösterilecek sayfa sayısı
	 * @var integer
	 */
	public $maxPages = 7;

	/**
	 * Sınıf başlatıcı
	 * @param $totalItems Toplam öğe sayısı
	 * @param $perPage Sayfa başına öğe sayısı
	 * @param $curPage Geçerli sayfa numarası
	 * @param $pattern Bağlantı kalıbı
	 */
	public function __construct($totalItems, $perPage, $curPage, $pattern)
	{
		$this->totalItems = $totalItems;
		$this->perPage = $perPage;
		$this->curPage = $curPage;
		$this->pattern = $pattern;

		$this->updateTotalPages();
	}

	/**
	 * Toplam sayfa sayısını hesaplar
	 */
	protected function updateTotalPages()
	{
		$this->totalPages =
			($this->perPage == 0 ? 0 : (int) ceil($this->totalItems / $this->perPage));
	}

	/**
	 * Gösterilecek en fazla sayfa sayısını belirler
	 * @param integer $maxPages
	 */
	public function setMaxPages($maxPages)
	{
		if ($maxPages > 3) {
			$this->maxPages = $maxPages;
		}
	}

	/**
	 * Geçerli sayfa numarasını belirler
	 * @param integer $curPage
	 */
	public function setCurPage($curPage)
	{
		$this->curPage = $curPage;
	}

	/**
	 * Sayfa başına gönderi sayısı
	 * @param integer $perPage
	 */
	public function setPerPage($perPage)
	{
		$this->perPage = $perPage;
		$this->updateTotalPages();
	}

	/**
	 * Toplam öğe sayısını belirler
	 * @param integer $totalItems
	 */
	public function setTotalItems($totalItems)
	{
		$this->totalItems = $totalItems;
		$this->updateTotalPages();
	}

	/**
	 * Bağlantı kalıbı belirler
	 * @param string $pattern
	 */
	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * Sayfa numarasına göre URL kalıbı döndürür
	 * @param integer $pageNum
	 * @return string
	 */
	public function getPageUrl($pageNum)
	{
		return str_replace($this->placeholder, $pageNum, $this->pattern);
	}

	/**
	 * Sonraki sayfa numarasını döndürür
	 * @return mixed
	 */
	public function getNextPage()
	{
		if ($this->curPage < $this->totalPages) {
			return $this->curPage + 1;
		}
		return false;
	}

	/**
	 * Bir önceki sayfa numarasını döndürür
	 * @return mixed
	 */
	public function getPrevPage()
	{
		if ($this->curPage > 1) {
			return $this->curPage - 1;
		}
		return false;
	}

	/**
	 * Bir sonraki sayfa URL'sini döndürür
	 * @return string
	 */
	public function getNextUrl()
	{
		if (!$this->getNextPage()) {
			return false;
		}
		return $this->getPageUrl($this->getNextPage());
	}

	/**
	 * Bir önceki sayfa URL'sini döndürür
	 * @return string
	 */
	public function getPrevUrl()
	{
		if (!$this->getPrevPage()) {
			return false;
		}
		return $this->getPageUrl($this->getPrevPage());
	}

	/**
	 * Sayfaları oluşturur
	 * @return array
	 */
	public function getPages()
	{
		$pages = [];

		if ($this->totalPages <= 1) {
			return [];
		}

		if ($this->totalPages <= $this->maxPages) {
			for ($i = 1; $i <= $this->totalPages; $i++) {
				$pages[] = $this->createPage($i, $i == $this->curPage);
			}
		} else {
			$numAdjacents = (int) floor(($this->maxPages - 3) / 2);

			if ($this->curPage + $numAdjacents > $this->totalPages) {
				$slidingStart = $this->totalPages - $this->maxPages + 2;
			} else {
				$slidingStart = $this->curPage - $numAdjacents;
			}

			if ($slidingStart < 2) $slidingStart = 2;

			$slidingEnd = $slidingStart + $this->maxPages - 3;

			if ($slidingEnd >= $this->totalPages) $slidingEnd = $this->totalPages - 1;


			$pages[] = $this->createPage(1, $this->curPage == 1);

			if ($slidingStart > 2) {
				$pages[] = $this->createPageEllipsis();
			}

			for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
				$pages[] = $this->createPage($i, $i == $this->curPage);
			}

			if ($slidingEnd < $this->totalPages - 1) {
				$pages[] = $this->createPageEllipsis();
			}

			$pages[] = $this->createPage($this->totalPages, $this->curPage == $this->totalPages);
		}

		return $pages;
	}

	/**
	 * Sayfa oluşturur
	 * @param integer $pageNum
	 * @param boolean $current
	 * @return array
	 */
	protected function createPage($pageNum, $current = false)
	{
		return [
			'num' => $pageNum,
			'url' => $this->getPageUrl($pageNum),
			'current' => $current
		];
	}

	/**
	 * ... oluşturur
	 * @return array
	 */
	protected function createPageEllipsis()
	{
		return [
			'num' => '...',
			'url' => null,
			'isCurrent' => false,
		];
	}

	/**
	 * Sayfaları HTML biçiminde döndürür
	 * @param boolean $pager
	 * @return string
	 */
	public function toHtml($pager = false)
	{
		if ($pager) {
			if ($this->totalPages <= 1) return null;

			$html = '<ul class="pager">';
			if ($this->getPrevUrl()) {
				$html .= '<li class="previous"><a href="' . $this->getPrevUrl() . '">&laquo; Önceki sayfa</a></li>';
			}

			if ($this->getNextUrl()) {
				$html .= '<li class="next"><a href="' . $this->getNextUrl() . '">Sonraki sayfa &raquo;</a></li>';
			}

			$html .= '</ul>';
		} else {
			if ($this->totalPages <= 1) return null;

			$html = '<ul class="pagination">';
			if ($this->getPrevUrl()) {
				$html .= '<li><a href="' . $this->getPrevUrl() . '">&laquo; Önceki</a></li>';
			}

			foreach ($this->getPages() as $page) {
				if ($page['url']) {
					$html .= '<li' . ($page['current'] ? ' class="active"' : null) . '><a href="' . $page['url'] . '">' . $page['num'] . '</a></li>';
				} else {
					$html .= '<li class="disabled"><span>' . $page['num'] . '</span></li>';
				}
			}

			if ($this->getNextUrl()) {
				$html .= '<li><a href="' . $this->getNextUrl() . '">Sonraki &raquo;</a></li>';
			}

			$html .= '</ul>';
		}
		return $html;
	}

	/**
	 * Sınıfı string tipine döndürür
	 */
	public function __toString()
	{
		return $this->toHtml() . null;
	}

	/**
	 * SQL Sorgusu için LIMIT döndürür
	 * 1,10 gibi
	 * @return string
	 */
	public function limit()
	{
		$limit = ($this->curPage * $this->perPage) - $this->perPage;
		return $limit . ',' . $this->perPage;
	}
}
