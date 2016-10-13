<?php

namespace DemirPHP;

/**
 * Sayfalama Sınıfı
 * Verileri sayfalama sınıfı
 * @author Yılmaz Demir <demiriy@gmail.com>
 * @link http://demirphp.com
 * @package DemirPHP\Pagination
 * @version 2.0
 */
	 
class Pagination
{
	/**
	 * @var string
	 */
	protected static $placeholder = ':num';

	/**
	 * @var integer
	 */
	protected static $totalItems;

	/**
	 * @var integer
	 */
	protected static $totalPages;

	/**
	 * @var integer
	 */
	protected static $perPage = 10;

	/**
	 * @var integer
	 */
	protected static $currentPage = 1;

	/**
	 * @var string
	 */
	protected static $url = '?page=:num';

	/**
	 * @var integer
	 */
	protected static $maxPages = 7;

	/**
	 * Sayfa sayısını günceller
	 */
	protected static function updateTotalPages()
	{
		self::$totalPages = (int) ceil(self::$totalItems / self::$perPage);
	}

	/**
	 * En fazla görüntülenecek sayfa numarası sayısını belirler
	 */
	public static function setMaxPages($maxPages)
	{
		if ($maxPages > 3) {
			self::$maxPages = $maxPages;
		}
		return new self;
	}

	/**
	 * Aktif olan sayfayı belirler
	 */
	public static function setCurrentPage($currentPage)
	{
		self::$currentPage = $currentPage;
		return new self;
	}

	/**
	 * Sayfa başına düşen öğe sayısını belirler
	 */
	public static function setPerPage($perPage)
	{
		self::$perPage = $perPage;
		self::updateTotalPages();
		return new self;
	}

	/**
	 * Toplam öğe sayısını belirler
	 */
	public static function setCount($count)
	{
		self::$totalItems = $count;
		self::updateTotalPages();
		return new self;
	}

	/**
	 * Sayfa URL'ini belirler
	 */
	public static function setPageUrl($url)
	{
		self::$url = $url;
		return new self;
	}

	/**
	 * Sayfa URL'ini döndürür
	 */
	public static function getPageUrl($number)
	{
		return str_replace(self::$placeholder, $number, self::$url);
	}

	/**
	 * Sonraki sayfa numarasını döndürür
	 */
	public static function getNextPage()
	{
		if (self::$currentPage < self::$totalPages) {
			return self::$currentPage + 1;
		}
		return FALSE;
	}

	/**
	 * Önceki sayfa numarasını döndürür
	 */
	public static function getPrevPage()
	{
		if (self::$currentPage > 1) {
			return self::$currentPage - 1;
		}
		return FALSE;
	}

	/**
	 * Sonraki sayfa URL'ini döndürür
	 */
	public static function getNextPageUrl()
	{
		if (self::getNextPage() === FALSE) {
			return FALSE;
		}
		return self::getPageUrl(self::getNextPage());
	}

	/**
	 * Önceki sayfa URL'ini döndürür
	 */
	public static function getPrevPageUrl()
	{
		if (self::getPrevPage() === FALSE) {
			return FALSE;
		}
		return self::getPageUrl(self::getPrevPage());
	}

	/**
	 * SQL için LIMIT döndürür örn. 1,10
	 */
	public static function getLimit()
	{
		$limit = (self::$currentPage * self::$perPage) - self::$perPage;
		return $limit . ',' . self::$perPage;
	}

	/**
	 * Sayfa numaraları dizesi için sayfa öğesi döndürür
	 */
	protected static function createPage($number, $current = FALSE, $ellipsis = FALSE)
	{
		if ($ellipsis !== FALSE) {
			return [
				'number' => '...',
				'current' => FALSE,
				'url' => NULL
			];
		}

		return [
			'number' => $number,
			'current' => $current,
			'url' => self::getPageUrl($number)
		];
	}

	/**
	 * Sayfa numaralarını dize olarak döndürür
	 */
	public static function getPages()
	{
		$pages = [];

		if (self::$totalPages <= 1) return $pages;

		if (self::$totalPages <= self::$maxPages) {
			for ($i=1; $i < self::$totalPages; $i++) { 
				$pages[] = self::createPage($i, $i == self::$currentPage);
			}
		} else {
			$numAdjacents = (int) floor((self::$maxPages - 3) / 2);
			if (self::$currentPage + $numAdjacents > self::$totalPages) {
				$slidingStart = self::$totalPages - self::$maxPages + 2;
			} else {
				$slidingStart = self::$currentPage - $numAdjacents;
			}
			if ($slidingStart < 2) $slidingStart = 2;
			$slidingEnd = $slidingStart + self::$maxPages - 3;
			if ($slidingEnd >= self::$totalPages) $slidingEnd = self::$totalPages - 1;
			$pages[] = self::createPage(1, self::$currentPage == 1);
			if ($slidingStart > 2) $pages[] = self::createPage(0, 0, TRUE);
			for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
				$pages[] = self::createPage($i, $i == self::$currentPage);
			}
			if ($slidingEnd < self::$totalPages - 1) $pages[] = self::createPage(0, 0, TRUE);
			$pages[] = self::createPage(self::$totalPages, self::$currentPage == self::$totalPages);
		}

		return $pages;
	}

	/**
	 * HTML sayfalandırma (önceki, sonraki) döndürür
	 */
	public static function getPagerAsHtml()
	{
		if (self::$totalPages < 1) return NULL;
		$html = '<ul class="pager">';
		if (self::getPrevPageUrl()) $html .= '<li><a href="' . self::getPrevPageUrl() . '">&laquo; Önceki Sayfa</a></li>';
		if (self::getNextPageUrl()) $html .= '<li><a href="' . self::getNextPageUrl() . '">Sonraki Sayfa &raquo;</a></li>';
		$html .= '</ul>';
		return $html;
	}

	/**
	 * HTML Sayfa numaralarını döndürür
	 */
	public static function getPagesAsHtml($pager = TRUE)
	{
		if (self::$totalPages <= 1) return NULL;
		$html = '<ul class="pager">';
		if ($pager && self::getPrevPageUrl()) $html .= '<li><a href="' . self::getPrevPageUrl() . '">&laquo; Önceki</a></li>';
		foreach (self::getPages() as $page) {
			if (is_null($page['url'])) {
				$html .= '<li class="disabled"><span>' . $page['number'] . '</span></li>';
			} else {
				$html .= '<li' . ($page['current'] ? ' class="active"' : NULL) . '><a href="' . $page['url'] . '">' . $page['number'] . '</a></li>';
			}
		}
		if ($pager && self::getNextPageUrl()) $html .= '<li><a href="' . self::getNextPageUrl() . '">Sonraki &raquo;</a></li>';
		$html .= '</ul>';

		return $html;
	}
}
