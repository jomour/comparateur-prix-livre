<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SeoService;
use App\Services\BreadcrumbService;

class SeoController extends Controller
{
    /**
     * Page principale du comparateur de prix manga
     */
    public function comparateurPrixManga(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('comparateur-prix-manga');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('comparateur-prix-manga');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page prix manga
     */
    public function prixManga(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('prix-manga');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('prix-manga');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page comparateur prix livres
     */
    public function comparateurPrixLivres(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('comparateur-prix-livres');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('comparateur-prix-livres');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page économiser manga
     */
    public function economiserManga(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('economiser-manga');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('economiser-manga');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page meilleur prix manga
     */
    public function meilleurPrixManga(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('meilleur-prix-manga');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('meilleur-prix-manga');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page manga price comparator (EN)
     */
    public function mangaPriceComparator(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('manga-price-comparator');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('manga-price-comparator');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page manga prices (EN)
     */
    public function mangaPrices(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('manga-prices');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('manga-prices');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page manga book price comparison (EN)
     */
    public function mangaBookPriceComparison(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('manga-book-price-comparison');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('manga-book-price-comparison');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page save money manga (EN)
     */
    public function saveMoneyManga(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('save-money-manga');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('save-money-manga');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page best manga price (EN)
     */
    public function bestMangaPrice(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('best-manga-price');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('best-manga-price');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
    
    /**
     * Page manga price checker (EN)
     */
    public function mangaPriceChecker(Request $request)
    {
        $isbn = $request->query('isbn');
        $meta = SeoService::getKeywordSpecificMeta('manga-price-checker');
        $seoType = 'website';
        $breadcrumbs = BreadcrumbService::generate('manga-price-checker');
        
        return view('price.search', compact('isbn', 'meta', 'seoType', 'breadcrumbs'));
    }
} 