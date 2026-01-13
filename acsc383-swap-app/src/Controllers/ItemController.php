<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helpers;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Services\ItemService;

final class ItemController
{
    private const CATEGORIES = ['Clothing','Books','Electronics','Furniture','Kitchen','Sports','Other'];
    private const CONDITIONS = ['New','Like New','Good','Fair','Needs Repair'];

    public function browse(Request $req): Response
    {
        $svc = new ItemService();

        $q = $req->input('q');
        $category = $req->input('category');
        $condition = $req->input('condition');
        $type = $req->input('type');

        $items = $svc->browse($q, $category, $condition, $type);

        return Response::html(\App\Core\View::render('items/browse', [
            'title' => 'Browse items',
            'items' => $items,
            'filters' => compact('q','category','condition','type'),
            'categories' => self::CATEGORIES,
            'conditions' => self::CONDITIONS,
        ]));
    }

    public function show(Request $req): Response
    {
        $id = (int)$req->input('id', '0');
        if ($id <= 0) return Response::redirect(Helpers::route('browse'));

        $svc = new ItemService();
        $item = $svc->get($id);
        if (!$item) {
            Helpers::flash('warning', 'Item not found.');
            return Response::redirect(Helpers::route('browse'));
        }

        $images = $svc->images($id);

        return Response::html(\App\Core\View::render('items/show', [
            'title' => (string)$item['title'],
            'item' => $item,
            'images' => $images,
        ]));
    }

    public function mine(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $svc = new ItemService();
        $items = $svc->mine(Auth::id() ?? 0);

        return Response::html(\App\Core\View::render('items/mine', [
            'title' => 'My items',
            'items' => $items,
        ]));
    }

    public function create(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $errors = [];
        $values = [
            'title' => $req->input('title'),
            'description' => $req->input('description'),
            'category' => $req->input('category'),
            'condition' => $req->input('condition'),
            'transaction_type' => $req->input('transaction_type'),
            'price' => $req->input('price'),
            'weight_kg' => $req->input('weight_kg'),

        ];

        if ($req->isPost()) {
            $v = new Validator();
            $v->required('title', $values['title'], 'Title is required.')
              ->minLen('title', $values['title'], 3, 'Title must be at least 3 characters.')
              ->required('description', $values['description'], 'Description is required.')
              ->minLen('description', $values['description'], 10, 'Description must be at least 10 characters.')
              ->required('category', $values['category'], 'Category is required.')
              ->required('condition', $values['condition'], 'Condition is required.')
              ->required('transaction_type', $values['transaction_type'], 'Transaction type is required.')
              ->minLen('weight_kg',$values['weight_kg'],0,'Weight(Kg) must be at least 0');
            $errors = $v->errors();

            // Sale requires price
            if ($values['transaction_type'] === 'SALE' && trim($values['price']) === '') {
                $errors['price'] = 'Price is required for sale items.';
            }

            // Basic category/condition validation
            if ($values['category'] !== '' && !in_array($values['category'], self::CATEGORIES, true)) {
                $errors['category'] = 'Invalid category.';
            }
            if ($values['condition'] !== '' && !in_array($values['condition'], self::CONDITIONS, true)) {
                $errors['condition'] = 'Invalid condition.';
            }
            if ($values['transaction_type'] !== '' && !in_array($values['transaction_type'], ['SWAP','SALE','DONATION'], true)) {
                $errors['transaction_type'] = 'Invalid transaction type.';
            }

            $w = trim($values['weight_kg']);
            if ($w === '' || !is_numeric($w) || (float)$w <= 0) {
                $errors['weight_kg'] = 'Enter a weight in kg greater than 0.';
            } else {
                // normalize to 2 decimals
                $values['weight_kg'] = number_format((float)$w, 2, '.', '');
            }


            if (empty($errors)) {
                $svc = new ItemService();
                $itemId = $svc->create(Auth::id() ?? 0, $values);

                // Upload images (multiple)
                $this->handleUploads($svc, $itemId);

                Helpers::flash('success', 'Item listed successfully.');
                return Response::redirect(Helpers::route('item_show', ['id' => $itemId]));
            }

            Helpers::flash('warning', 'Please fix the highlighted fields.');
        }

        return Response::html(\App\Core\View::render('items/create', [
            'title' => 'List an item',
            'errors' => $errors,
            'values' => $values,
            'categories' => self::CATEGORIES,
            'conditions' => self::CONDITIONS,
        ]));
    }

    public function edit(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $id = (int)$req->input('id', '0');
        if ($id <= 0) return Response::redirect(Helpers::route('my_items'));

        $svc = new ItemService();
        $item = $svc->get($id);

        if (!$item || (int)$item['owner_id'] !== (Auth::id() ?? -1)) {
            Helpers::flash('danger', 'Not allowed.');
            return Response::redirect(Helpers::route('my_items'));
        }

        if ((string)$item['status'] !== 'AVAILABLE') {
            Helpers::flash('warning', 'You can only edit items that are available.');
            return Response::redirect(Helpers::route('my_items'));
        }

        $errors = [];
        $values = [
            'title' => $req->input('title', (string)$item['title']),
            'description' => $req->input('description', (string)$item['description']),
            'category' => $req->input('category', (string)$item['category']),
            'condition' => $req->input('condition', (string)$item['condition']),
            'transaction_type' => $req->input('transaction_type', (string)$item['transaction_type']),
            'price' => $req->input('price', (string)($item['price'] ?? '')),
            'weight_kg' => $req->input('weight_kg', (string)($item['weight_kg'] ?? '0')),
            ];

        if ($req->isPost()) {
            $v = new Validator();
            $v->required('title', $values['title'], 'Title is required.')
              ->minLen('title', $values['title'], 3, 'Title must be at least 3 characters.')
              ->required('description', $values['description'], 'Description is required.')
              ->minLen('description', $values['description'], 10, 'Description must be at least 10 characters.')
              ->required('category', $values['category'], 'Category is required.')
              ->required('condition', $values['condition'], 'Condition is required.')
              ->required('transaction_type', $values['transaction_type'], 'Transaction type is required.')
              ->minLen('weight_kg',$values['weight_kg'],0,'Weight(Kg) must be at least 0');

            $errors = $v->errors();

            if ($values['transaction_type'] === 'SALE' && trim($values['price']) === '') {
                $errors['price'] = 'Price is required for sale items.';
            }

            if ($values['category'] !== '' && !in_array($values['category'], self::CATEGORIES, true)) {
                $errors['category'] = 'Invalid category.';
            }
            if ($values['condition'] !== '' && !in_array($values['condition'], self::CONDITIONS, true)) {
                $errors['condition'] = 'Invalid condition.';
            }
            if ($values['transaction_type'] !== '' && !in_array($values['transaction_type'], ['SWAP','SALE','DONATION'], true)) {
                $errors['transaction_type'] = 'Invalid transaction type.';
            }

            if (empty($errors)) {
                $ok = $svc->update($id, Auth::id() ?? 0, $values);
                $this->handleUploads($svc, $id);

                Helpers::flash($ok ? 'success' : 'warning', $ok ? 'Item updated.' : 'Nothing changed (or item no longer editable).');
                return Response::redirect(Helpers::route('item_show', ['id' => $id]));
            }

            Helpers::flash('warning', 'Please fix the highlighted fields.');
        }

        $images = $svc->images($id);

        return Response::html(\App\Core\View::render('items/edit', [
            'title' => 'Edit item',
            'errors' => $errors,
            'values' => $values,
            'item' => $item,
            'images' => $images,
            'categories' => self::CATEGORIES,
            'conditions' => self::CONDITIONS,
        ]));
    }

    public function delete(Request $req): Response
    {
        if (!Auth::check()) return Response::redirect(Helpers::route('login'));

        $id = (int)$req->input('id', '0');
        if ($id <= 0) return Response::redirect(Helpers::route('my_items'));

        $svc = new ItemService();
        $ok = $svc->delete($id, Auth::id() ?? 0);

        Helpers::flash($ok ? 'info' : 'warning', $ok ? 'Item deleted.' : 'Unable to delete (maybe not yours or not available).');
        return Response::redirect(Helpers::route('my_items'));
    }

    private function handleUploads(ItemService $svc, int $itemId): void
    {
        if (!isset($_FILES['images'])) return;

        $files = $_FILES['images'];
        if (!is_array($files['name'])) return;

        $baseDir = __DIR__ . '/../../public/uploads/items/' . $itemId;
        if (!is_dir($baseDir)) @mkdir($baseDir, 0777, true);

        $maxBytes = (defined('MAX_UPLOAD_MB') ? MAX_UPLOAD_MB : 5) * 1024 * 1024;
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;

            $tmp = $files['tmp_name'][$i];
            $size = (int)($files['size'][$i] ?? 0);
            if ($size <= 0 || $size > $maxBytes) continue;

            $mime = mime_content_type($tmp) ?: '';
            if (!isset($allowed[$mime])) continue;

            $ext = $allowed[$mime];
            $name = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;

            $destFs = $baseDir . '/' . $name;
            if (move_uploaded_file($tmp, $destFs)) {
                // Web path stored in DB
                $webPath = 'uploads/items/' . $itemId . '/' . $name;
                $svc->addImage($itemId, $webPath);
            }
        }
    }
}
