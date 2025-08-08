<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\StoreDocumentRequest;
use App\Http\Requests\Document\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen yang dimiliki oleh pengguna yang sedang login.
     * Hasilnya dipaginasi untuk efisiensi.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $documents = Document::where('user_id', Auth::id())
            ->with('user')
            ->latest()
            ->paginate(10);

        return DocumentResource::collection($documents);
    }

    /**
     * Menyimpan dokumen baru yang diunggah oleh pengguna ke Supabase.
     * Validasi ditangani oleh StoreDocumentRequest.
     *
     * @param  \App\Http\Requests\Document\StoreDocumentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
public function store(StoreDocumentRequest $request)
{
    $file = $request->file('document');

    $bucket = env('SUPABASE_BUCKET');
    $fileName = time() . '_' . $file->getClientOriginalName();
    $filePath = "documents/{$fileName}";

    try {

        $response = Http::withToken(env('SUPABASE_SERVICE_ROLE_KEY'))
            ->attach('file', file_get_contents($file), $fileName)
              ->post(env('SUPABASE_URL') . "/storage/v1/object/{$bucket}/{$filePath}");

        if (!$response->successful()) {
            throw new \Exception($response->body());
        }

        Log::info('File berhasil diunggah ke Supabase. Path: ' . $filePath);
    } catch (\Exception $e) {
        Log::error('GAGAL UPLOAD KE SUPABASE: ' . $e->getMessage());
        return response()->json([
            'message' => 'Gagal mengunggah file ke Supabase.',
            'error' => $e->getMessage()
        ], 500);
    }

   
    $hash = hash_file('sha256', $file->getRealPath());

    $document = Document::create([
        'title' => $request->title,
        'file_path' => $filePath,
        'original_hash' => $hash,
        'status' => 'draft',
        'user_id' => Auth::id(),
        'group_id' => $request->group_id,
    ]);

    return response()->json([
        'message' => 'Dokumen berhasil diunggah ke Supabase.',
        'document' => new DocumentResource($document)
    ], 201);
}

    /**
     * Menampilkan detail dari satu dokumen spesifik.
     * Hak akses dicek menggunakan DocumentPolicy.
     *
     * @param  \App\Models\Document  $document
     * @return \App\Http\Resources\DocumentResource
     */
    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return new DocumentResource($document->load('user'));
    }

    /**
     * Memperbarui detail dokumen (judul).
     * Validasi dan hak akses ditangani oleh UpdateDocumentRequest dan DocumentPolicy.
     *
     * @param  \App\Http\Requests\Document\UpdateDocumentRequest  $request
     * @param  \App\Models\Document  $document
     * @return \App\Http\Resources\DocumentResource
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $this->authorize('update', $document);

        $validated = $request->validated();

        $document->update($validated);

        return new DocumentResource($document);
    }

    /**
     * Menghapus dokumen dari database dan file dari Supabase.
     * Hak akses dicek menggunakan DocumentPolicy.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        Storage::disk('supabase')->delete($document->file_path);

        $document->delete();

        return response()->json(['message' => 'Dokumen berhasil dihapus.'], 200);
    }
}