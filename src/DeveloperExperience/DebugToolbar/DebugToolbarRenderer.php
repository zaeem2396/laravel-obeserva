<?php

declare(strict_types=1);

namespace Obeserva\DeveloperExperience\DebugToolbar;

use Obeserva\DeveloperExperience\TraceTreeNode;

final class DebugToolbarRenderer
{
    public function renderHtml(DebugToolbarPayload $payload): string
    {
        $json = json_encode($payload->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $summary = sprintf(
            '%d spans · %.2f ms · trace %s',
            $payload->spanCount,
            $payload->totalDurationMs,
            $payload->propagation->traceId ?? 'n/a',
        );

        $treeHtml = $this->renderTree($payload->traceTree);

        return <<<HTML
<div id="obeserva-debug-toolbar" style="position:fixed;bottom:0;left:0;right:0;z-index:99999;background:#1e1e2e;color:#cdd6f4;font:12px/1.4 ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;border-top:2px solid #89b4fa;max-height:40vh;overflow:auto;">
  <div style="padding:8px 12px;background:#313244;display:flex;justify-content:space-between;align-items:center;cursor:pointer;" onclick="document.getElementById('obeserva-debug-body').classList.toggle('obeserva-hidden')">
    <strong style="color:#89b4fa;">Obeserva</strong>
    <span>{$summary}</span>
  </div>
  <div id="obeserva-debug-body" style="padding:12px;">
    <div style="margin-bottom:8px;color:#a6adc8;">Propagation: queue spans {$this->countList($payload->propagation->queueSpans)}, HTTP spans {$this->countList($payload->propagation->httpSpans)}</div>
    {$treeHtml}
    <script type="application/json" id="obeserva-debug-data">{$json}</script>
  </div>
</div>
<style>.obeserva-hidden{display:none!important}.obeserva-span-row{padding:2px 0;border-left:2px solid #45475a;margin-left:8px;padding-left:8px}</style>
HTML;
    }

    /**
     * @param  list<TraceTreeNode>  $nodes
     */
    private function renderTree(array $nodes, int $depth = 0): string
    {
        if ($nodes === []) {
            return '<div style="color:#6c7086;">No spans recorded.</div>';
        }

        $html = '';

        foreach ($nodes as $node) {
            $duration = $node->snapshot->duration !== null
                ? sprintf('%.2f ms', $node->snapshot->duration * 1000)
                : 'active';
            $indent = str_repeat('&nbsp;', $depth * 4);
            $name = htmlspecialchars($node->snapshot->name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $kind = htmlspecialchars($node->snapshot->kind, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $html .= "<div class=\"obeserva-span-row\">{$indent}<span style=\"color:#f9e2af;\">{$name}</span> <span style=\"color:#6c7086;\">({$kind})</span> <span style=\"color:#a6e3a1;\">{$duration}</span></div>";
            $html .= $this->renderTree($node->children, $depth + 1);
        }

        return $html;
    }

    /**
     * @param  list<string>  $items
     */
    private function countList(array $items): string
    {
        return (string) count($items);
    }
}
