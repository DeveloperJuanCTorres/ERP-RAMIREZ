<div class="container my-3" id="menuAccordion">
    <div class="row g-3">
        @foreach($items as $item)
            @php
                $icon = $item->attr('icon') ?? 'fa fa-circle';
                $id = 'menu-' . $item->id;
            @endphp

            @if($item->hasChildren())
                {{-- Botón padre con acordeón --}}
                <div class="col-6">
                    <button class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center py-4 shadow-sm rounded-3"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $id }}"
                            aria-expanded="false"
                            aria-controls="{{ $id }}"
                            style="height: 120px;">
                        <i class="{{ $icon }} fa-2x mb-2"></i>
                        <span class="fw-bold text-center">{{ $item->title }}</span>
                    </button>
                </div>

                {{-- Contenedor hijos en grid --}}
                <div class="collapse custom-collapse w-100 mt-2" id="{{ $id }}" data-bs-parent="#menuAccordion">
                    <div class="row g-3">
                        @foreach($item->children() as $sub)
                            @php $subIcon = $sub->attr('icon') ?? 'fa fa-circle'; @endphp
                            <div class="col-6">
                                <a href="{{ $sub->url() }}"
                                   class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center justify-content-center py-3 shadow-sm rounded-3"
                                   style="height: 100px;">
                                    <i class="{{ $subIcon }} fa-lg mb-1"></i>
                                    <span class="small text-center">{{ $sub->title }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Botón sin hijos --}}
                <div class="col-6">
                    <a href="{{ $item->url() }}"
                       class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center py-4 shadow-sm rounded-3"
                       style="height: 120px;">
                        <i class="{{ $icon }} fa-2x mb-2"></i>
                        <span class="fw-bold text-center">{{ $item->title }}</span>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</div>