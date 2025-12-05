<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class DictionaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // $words = Dictionary::all();
        // foreach($words as $word){
        //     $word->sound = 'https://translate.google.com/translate_tts?ie=UTF-&&client=tw-ob&tl=en&q='.str_replace(' ', '%20', $word->word);
        //     $word->save();
        // }
        // $words = Dictionary::limit(20)  // Ограничивает вывод 10 записями
        // ->offset(160) // Пропускает первые 20 записей
        // ->get();
        // foreach($words as $word){
        //     $wordInfo = $this->updateWord($word->word);
        //     Log::info([$wordInfo, $word->id]);

        //     sleep(1);
        //     $translations = '';
        //     $definitions = '';
        //     $examples = '';
        //     if(!isset($wordInfo["word"])){
        //         continue;
        //     }
        //     $sound = 'https://translate.google.com/translate_tts?ie=UTF-&&client=tw-ob&tl=en&q='.str_replace(' ', '%20', $wordInfo["word"]);

        //     if($wordInfo["translation"] !== NULL){
        //         foreach($wordInfo["popular_translations"] as $ptranslate){
        //             $translations .= $ptranslate.', ';
        //         }

        //         foreach($wordInfo["definitions"] as $index => $definition){
        //             $definitions .= ($index+1).'. '.$definition.PHP_EOL;
        //         }

        //         foreach($wordInfo["examples"] as $index => $example){
        //             $examples .= ($index+1).'. '.$example.PHP_EOL;
        //         }
        //     }
        //     $translations = rtrim($translations, ', ');

        //     $word->fill([
        //         'translation' => $translations,
        //         'definition' => $definitions,
        //         'example' => $examples,
        //         'sound' => $sound,
        //     ]);

        //     $word->save();

        // }
        
            
        

        $dictionary = Dictionary::orderBy('word', 'ASC')->paginate(50);
        return view('dashboard.words', compact(['dictionary']));
    }


    public function updateWord($word)
    {
        $messages = 
        'Переведи с английского это слово или выражение: "' . $word. '" ответ мне дай в массиве, где будут отображены следующие пункты:
        "word" - "English version of word or expression",
        "translation" - "translation in Russian",
        "popular_translations" -"variant_1","variant_2","variant_3".
        "definitions" - "Definition of variant 1 in Russian.","Definition of variant 2 in Russian.","Definition of variant 3 in Russian.",
        "examples" - "Example of variant 1 in English","Example of variant 2 in English","Example of variant 3 in English,

        если полученный текст выглядит как набор букв, то отправь в поле translation NULL.
        ';

        $guzzle = new Client();

        try {
            $response = $guzzle->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-8b:generateContent', [
                'query' => ['key' => env('GEMINI_API_KEY')], // Ваш API ключ
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $messages]
                            ]
                        ]
                    ]
                ]
            ]);

        } catch (RequestException $e) {

            Log::error('Guzzle error: ', ['exception' => $e->getMessage()]);
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody(true), true);
            }
            throw $e; 
        }

        $response = json_decode($response->getBody()->getContents())->candidates;

        // Извлекаем текст ответа
        $response = $response[0]->content->parts[0]->text;

        // Удаляем лишние символы (например, троичные кавычки и пробелы)
        $responseText = preg_replace('/^```json|\s*$/', '', $response); // Убираем начальные и конечные лишние символы
        $responseText = preg_replace('/```/', '', $responseText); // Убираем все остальное, если есть дополнительные ```

        return json_decode($responseText, true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dictionary $dictionary)
    {
        if($dictionary->delete()){
            return redirect(route('dictionary.index'))->with(['success' => 'Слово удалено']);
        }
        return redirect(route('dictionary.index'))->withErrors('error', 'Что-то пошло не так.');
    }

    public function search(Request $request){
        $query = $request->get('query');

        $linkByName = Dictionary::where('word', 'LIKE', "%$query%")->get();
        $linkByUrl = Dictionary::where('translation', 'LIKE', "%$query%")->get();

        $mergedLinks = $linkByName->merge($linkByUrl)->unique('id');
        $mergedLinks = $mergedLinks->sortBy('word');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50; 
        $currentItems = $mergedLinks->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedLinks = new LengthAwarePaginator($currentItems, $mergedLinks->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('dashboard.words', ['dictionary' => $paginatedLinks])->render(); 
    }
}
