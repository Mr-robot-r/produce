<?php
namespace App\Repositories\Eloquent;

use App\Models\BOM;
use App\Repositories\Contracts\BOMRepositoryInterface;
use Illuminate\Support\Collection;

class BOMRepository implements BOMRepositoryInterface
{
    public function loadBOMTree(int $productId): array
    {
        $allBoms = BOM::with('childProduct')->get()->groupBy('parent_product_id');
        return $this->buildTree($productId, $allBoms);
    }

    private function buildTree(int $productId, Collection $groupedBoms): array
    {
        $tree = [];
        $children = $groupedBoms->get($productId, collect());
        foreach ($children as $bom) {
            $tree[] = [
                'bom' => $bom,
                'children' => $this->buildTree($bom->child_product_id, $groupedBoms)
            ];
        }
        return $tree;
    }

    public function hasCycle(int $productId): bool
    {
        $visited = [];
        $stack = [];
        $edges = BOM::select('parent_product_id', 'child_product_id')->get();
        $graph = $edges->groupBy('parent_product_id')->map->pluck('child_product_id')->toArray();

        return $this->dfsCycle($productId, $graph, $visited, $stack);
    }

    private function dfsCycle($node, $graph, &$visited, &$stack): bool
    {
        if (in_array($node, $stack))
            return true;
        if (in_array($node, $visited))
            return false;

        $visited[] = $node;
        $stack[] = $node;

        foreach ($graph[$node] ?? [] as $child) {
            if ($this->dfsCycle($child, $graph, $visited, $stack)) {
                return true;
            }
        }
        array_pop($stack);
        return false;
    }
}