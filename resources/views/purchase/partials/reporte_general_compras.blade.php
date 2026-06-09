@extends('layouts.app')

@section('title', 'Reporte Compras por Proveedor')

@section('content')

<section class="content-header">
    <h1>Reporte Compras por Proveedor</h1>
</section>

<section class="content">

<div class="box box-primary">

    <div class="box-header">
        <h3 class="box-title">Filtros</h3>
    </div>

    <div class="box-body">

        <form method="POST" action="/reporte-compras-proveedor/generar">

            @csrf

            <div class="row">

                <div class="col-md-4">
                    <label>Proveedor</label>

                    <select name="proveedor_id"
                            class="form-control select2"
                            required>

                        <option value="">
                            Seleccionar
                        </option>

                        @foreach($proveedores as $proveedor)

                        <option value="{{$proveedor->id}}">
                            {{$proveedor->supplier_business_name}}
                        </option>

                        @endforeach

                    </select>
                </div>

                <div class="col-md-3">
                    <label>Fecha Inicio</label>

                    <input type="date"
                           name="fecha_inicio"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-3">
                    <label>Fecha Fin</label>

                    <input type="date"
                           name="fecha_fin"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>

                    <button type="submit"
                            class="btn btn-primary btn-block">

                        Generar
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

</section>

@endsection