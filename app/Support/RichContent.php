<?php

namespace App\Support;

class RichContent
{
    /**
     * Parse and render rich content containing text, images (url or base64),
     * videos (YouTube, MP4, embed), and syntax code blocks safely.
     */
    public static function render(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // 1. Process fenced code blocks ```lang ... ``` first to protect code from other parsers
        $codeBlocks = [];
        $content = preg_replace_callback('/```([a-zA-Z0-9_\-\+]*)\n([\s\S]*?)```/', function ($matches) use (&$codeBlocks) {
            $key = '___CODE_BLOCK_'.count($codeBlocks).'___';
            $lang = htmlspecialchars(trim($matches[1]) ?: 'code');
            $code = htmlspecialchars($matches[2]);
            $codeBlocks[$key] = '<div class="my-3 rounded-xl overflow-hidden border border-slate-800 shadow-sm">'
                .'<div class="bg-slate-950 px-4 py-1.5 text-[11px] font-mono text-slate-400 flex items-center justify-between border-b border-slate-800">'
                .'<span class="uppercase tracking-wider font-semibold text-emerald-400">'.$lang.'</span>'
                .'<span class="text-[10px] text-slate-500">Source Code</span>'
                .'</div>'
                .'<pre class="bg-slate-900 text-slate-100 p-4 font-mono text-xs overflow-x-auto leading-relaxed"><code>'.$code.'</code></pre>'
                .'</div>';

            return $key;
        }, $content);

        // Escape all authored HTML while keeping the supported Markdown-like syntax intact.
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // 2. Process inline code `...`
        $inlineCodes = [];
        $content = preg_replace_callback('/`([^`\n]+)`/', function ($matches) use (&$inlineCodes) {
            $key = '___INLINE_CODE_'.count($inlineCodes).'___';
            $inlineCodes[$key] = '<code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-xs font-semibold text-indigo-600 border border-slate-200">'.$matches[1].'</code>';

            return $key;
        }, $content);

        // 3. Process video embeds: [video:URL] or direct YouTube URLs
        $content = preg_replace_callback('/(?:\[video:(https?:\/\/[^\]\s]+)\]|(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_\-]{11}))/', function ($matches) {
            if (! empty($matches[2])) {
                $youtubeId = htmlspecialchars($matches[2]);

                return '<div class="aspect-video my-3 rounded-xl overflow-hidden shadow-sm border border-slate-200">'
                    .'<iframe class="w-full h-full" src="https://www.youtube.com/embed/'.$youtubeId.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                    .'</div>';
            }
            if (! empty($matches[1])) {
                $url = $matches[1];
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $url, $ytMatches)) {
                    return '<div class="aspect-video my-3 rounded-xl overflow-hidden shadow-sm border border-slate-200">'
                        .'<iframe class="w-full h-full" src="https://www.youtube.com/embed/'.$ytMatches[1].'" frameborder="0" allowfullscreen></iframe>'
                        .'</div>';
                }

                return '<div class="my-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 bg-black">'
                    .'<video controls class="w-full max-h-96 rounded-xl" src="'.$url.'"></video>'
                    .'</div>';
            }

            return $matches[0];
        }, $content);

        // 4. Process markdown images: ![alt](url_or_base64)
        $content = preg_replace_callback('/!\[([^\]]*)\]\((data:image\/[^;]+;base64,[^\)]+|https?:\/\/[^\)]+|\/[^\)]+)\)/', function ($matches) {
            $alt = $matches[1] ?: 'Gambar';
            $src = $matches[2]; // Can be data URI or URL

            return '<div class="my-3 text-center">'
                .'<img src="'.$src.'" alt="'.$alt.'" class="inline-block max-w-full max-h-96 rounded-xl border border-slate-200 shadow-sm object-contain bg-slate-50 p-1" loading="lazy" />'
                .($alt !== 'Gambar' ? '<span class="block text-[11px] text-slate-400 mt-1 italic">'.$alt.'</span>' : '')
                .'</div>';
        }, $content);

        // 5. Restore code blocks & inline codes
        foreach ($codeBlocks as $key => $html) {
            $content = str_replace($key, $html, $content);
        }
        foreach ($inlineCodes as $key => $html) {
            $content = str_replace($key, $html, $content);
        }

        // 6. Convert newlines to breaks where appropriate
        // Split by blocks to avoid messing up pre/code blocks
        $parts = explode('<div class="my-3', $content);
        $finalParts = [];
        foreach ($parts as $idx => $part) {
            if ($idx === 0) {
                $finalParts[] = nl2br($part);
            } else {
                $subParts = explode('</div>', $part, 2);
                $divContent = '<div class="my-3'.$subParts[0].'</div>';
                $rest = isset($subParts[1]) ? nl2br($subParts[1]) : '';
                $finalParts[] = $divContent.$rest;
            }
        }

        return implode('', $finalParts);
    }
}
