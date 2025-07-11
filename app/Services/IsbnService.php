<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IsbnService
{
    /**
     * Récupère le titre d'un livre via son ISBN
     */
    public function getTitleFromIsbn($isbn)
    {
        try {
            // API OpenLibrary
            $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
            $response = Http::get($url);
            $data = $response->json();

            if (isset($data["ISBN:{$isbn}"]) && isset($data["ISBN:{$isbn}"]['title'])) {
                return $data["ISBN:{$isbn}"]['title'];
            }

            // Fallback: essayer Google Books API
            $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}";
            $response = Http::get($url);
            $data = $response->json();

            if (isset($data['items'][0]['volumeInfo']['title'])) {
                return $data['items'][0]['volumeInfo']['title'];
            }

            // Si aucune API ne fonctionne, retourner un titre par défaut
            return "Livre ISBN: {$isbn}";

        } catch (\Exception $e) {
            // En cas d'erreur, retourner un titre par défaut
            return "Livre ISBN: {$isbn}";
        }
    }

    /**
     * Vérifie si un ISBN est valide
     */
    public function isValidIsbn($isbn)
    {
        $length = strlen($isbn);
        
        // ISBN-10 ou ISBN-13
        if ($length !== 10 && $length !== 13) {
            return false;
        }

        // Vérification de la checksum pour ISBN-10
        if ($length === 10) {
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += (10 - $i) * intval($isbn[$i]);
            }
            $checkDigit = $isbn[9] === 'X' ? 10 : intval($isbn[9]);
            return ($sum + $checkDigit) % 11 === 0;
        }

        // Vérification de la checksum pour ISBN-13
        if ($length === 13) {
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += intval($isbn[$i]) * ($i % 2 === 0 ? 1 : 3);
            }
            $checkDigit = intval($isbn[12]);
            return (10 - ($sum % 10)) % 10 === $checkDigit;
        }

        return false;
    }

    /**
     * Récupère les informations complètes d'un livre via son ISBN
     */
    public function getBookInfo($isbn)
    {
        try {
            // Essayer OpenLibrary d'abord
            $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data["ISBN:{$isbn}"])) {
                $book = $data["ISBN:{$isbn}"];
                return [
                    'title' => $book['title'] ?? 'Titre inconnu',
                    'author' => isset($book['authors'][0]['name']) ? $book['authors'][0]['name'] : null,
                    'publisher' => isset($book['publishers'][0]['name']) ? $book['publishers'][0]['name'] : null,
                    'published_date' => $book['publish_date'] ?? null
                ];
            }

            // Fallback: Google Books API
            $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}";
            $response = Http::timeout(10)->get($url);
            $data = $response->json();

            if (isset($data['items'][0]['volumeInfo'])) {
                $book = $data['items'][0]['volumeInfo'];
                return [
                    'title' => $book['title'] ?? 'Titre inconnu',
                    'author' => isset($book['authors'][0]) ? $book['authors'][0] : null,
                    'publisher' => $book['publisher'] ?? null,
                    'published_date' => $book['publishedDate'] ?? null
                ];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extrait un prix numérique d'une chaîne de caractères
     */
    public function extractPriceFromString($priceString)
    {
        if (!$priceString || $priceString === "Prix non trouvé") {
            return null;
        }
        
        // Nettoyer la chaîne
        $price = trim($priceString);
        
        // Supprimer le symbole euro et autres caractères
        $price = str_replace(['€', 'EUR', 'euros', 'euro'], '', $price);
        $price = preg_replace('/[^\d.,]/', '', $price);
        
        // Gérer les virgules et points
        if (strpos($price, ',') !== false && strpos($price, '.') !== false) {
            // Format "1,234.56" ou "1.234,56"
            if (strpos($price, ',') < strpos($price, '.')) {
                // "1,234.56" -> virgule pour milliers
                $price = str_replace(',', '', $price);
            } else {
                // "1.234,56" -> point pour milliers, virgule pour décimales
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
            }
        } else {
            // Format simple avec virgule ou point
            $price = str_replace(',', '.', $price);
        }
        
        $numericPrice = floatval($price);
        
        // Vérifier que c'est un prix valide
        if ($numericPrice > 0 && $numericPrice < 10000) {
            return $numericPrice;
        }
        
        return null;
    }
} 