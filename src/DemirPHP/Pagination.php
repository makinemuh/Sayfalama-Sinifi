<?php

namespace DemirPHP;

class Pagination
{
	/**
	 * @var integer
	 */
	public static $totalItems;

	/**
	 * @var integer
	 */
	public static $totalPages;

	/**
	 * @var integer
	 */
	public static $currentPage;

	/**
	 * @var integer
	 */
	public static $perPage = 10;

	/**
	 * @var string
	 */
	public static $placeholder = ':number';

	/**
	 * @var string
	 */
	public static $url = '?page=:number';

	/**
	 * @return integer
	 */
	public static function getTotalItems()
	{
		return self::$totalItems;
	}

	/**
	 * @param integer $totalItems
	 * @return null
	 */
	public static function setTotalItems($totalItems)
	{
		self::$totalItems = (int) $totalItems;
		self::setTotalPages();
	}

	/**
	 * @return integer
	 */
	public static function getTotalPages()
	{
		return self::$totalPages;
	}

	/**
	 * @return integer
	 */
	public static function setTotalPages()
	{
		return self::$totalPages = (int) ceil( self::$totalItems / self::$perPage );
	}

	/**
	 * @param integer $perPage
	 * @return null
	 */
	public static function setPerPage($perPage)
	{
		self::$perPage = (int) $perPage;
		self::setTotalPages();
	}

	/**
	 * @return integer
	 */
	public static function getCurrentPage()
	{
		return self::$currentPage;
	}

	/**
	 * @param integer
	 * @return integer
	 */
	public static function setCurrentPage($currentPage)
	{
		return self::$currentPage = (int) $currentPage;
	}

	/**
	 * @param integer
	 * @return string
	 */
	public static function getUrl($page)
	{
		return str_replace( self::$placeholder, $page, self::$url );
	}

	/**
	 * @param string $url
	 * @return string 
	 */
	public static function setUrl($url)
	{
		return self::$url = $url;
	}

	/**
	 * @return integer|boolean
	 */
	public static function getNextPage()
	{
		if ( self::$currentPage < self::$totalPages ) {
			return self::$currentPage + 1;
		}
		return false;
	}

	/**
	 * @return integer|boolean
	 */
	public static function getPrevPage()
	{
		if ( self::$currentPage > 1 ) {
			return self::$currentPage - 1;
		}
		return false;
	}

	/**
	 * @return boolean|string
	 */
	public static function getNextUrl()
	{
		if ( !self::getNextPage() ) {
			return false;
		}
		return self::getUrl( self::getNextPage() );
	}

	/**
	 * @return boolean|string
	 */
	public static function getPrevUrl()
	{
		if ( !self::getPrevPage() ) {
			return false;
		}
		return self::getUrl( self::getPrevPage() );
	}

	/**
	 * @return boolean|string
	 */
	public static function getFirstUrl()
	{
		if (self::$currentPage === 1) {
			return false;
		}
		return self::getUrl(1);
	}

	/**
	 * @return string
	 */
	public static function getLastUrl()
	{
		if (self::$currentPage === self::$totalPages) {
			return false;
		}
		return self::getUrl(self::$totalPages);
	}

	/**
	 * @return integer
	 */
	public static function getLimit()
	{
		return self::$perPage;
	}

	/**
	 * @return integer
	 */
	public static function getOffset()
	{
		return (self::$currentPage * self::$perPage) - self::$perPage;
	}

	/**
	 * @return array
	 */
	public static function getPages()
	{
		$pages = [];

		if (self::$totalPages <= 1) return false;
		for ($i=1; $i <= self::$totalPages; $i++) {
			$current = self::$currentPage === $i;
			if ($current) $pages[$i]['active'] = true;
			$pages[$i]['number'] = $i;
			$pages[$i]['url'] = self::getUrl($i);
		}
		return $pages;
	}
}
