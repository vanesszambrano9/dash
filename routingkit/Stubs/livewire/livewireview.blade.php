@verbatim
    @if ($this->pagina)
        <x-landing.v1.page-renderer :pageVersion="$this->pagina->versionActiva" />
    @else
        <x-landing.v1.pages.atencion-cooperativista.costo-anual-total />
    @endif
@endverbatim
