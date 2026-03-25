@if($paginator->hasPages() || $paginator->total() > 0)
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 px-2">
    <div class="text-muted" style="font-size: 13px;">
        Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries
    </div>
    @if($paginator->hasPages())
        <div>
            {{ $paginator->withQueryString()->links() }}
        </div>
    @endif
</div>
@endif
