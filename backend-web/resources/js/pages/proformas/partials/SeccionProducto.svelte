<script lang="ts">
  import type { InertiaForm } from '@inertiajs/svelte';
  import type { MouseEventHandler } from 'svelte/elements';
  import * as v from 'valibot';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import Autocomplete from '@/components/_ui/autocomplete.svelte';
  import type { Product, ProductItem } from '@/types/product';

  // HACK: tomar solo propiedades que se necesitan
  type Props = {
    form: InertiaForm<any>;
    onAddProduct?: (productItem: ProductItem) => void;
  };
  let { form, onAddProduct }: Props = $props();

  let productItem = $state<ProductItem>({
    id: 0,
    codigo: '',
    cantidad: 0,
    descripcion: '',
    precio: 0,
    unidad_medida: '',
    activo: true,
  });

  const productItemSchema = v.object({
    id: v.pipe(v.number(), v.minValue(1, 'Debe seleccionar un producto')),
    codigo: v.pipe(v.string(), v.nonEmpty('Debe seleccionar un codigo')),
    descripcion: v.pipe(
      v.string(),
      v.nonEmpty('Debe seleccionar una descripcion'),
    ),
    precio: v.pipe(
      v.number('Debe ingresar un precio'),
      v.minValue(1, 'Debe ingresar un precio mayor a 0'),
    ),
    cantidad: v.pipe(
      v.number('Debe ingresar una cantidad'),
      v.minValue(1, 'Debe ingresar una cantidad mayor a 0'),
    ),
  });

  const addProduct: MouseEventHandler<HTMLButtonElement> = (_) => {
    const result = v.safeParse(productItemSchema, productItem);

    if (!result.success) {
      alert(result.issues[0].message);

      return;
    }

    onAddProduct?.(productItem);
  };

  const handleAcceptCode = async (codigo: string) => {
    const response = await fetch(
      ProductController.get.url({ query: { q: codigo, by: 'codigo' } }),
    );
    const producto = (await response.json()) as Product;

    productItem = {
      id: producto.id,
      codigo: producto.codigo,
      cantidad: 1,
      descripcion: producto.descripcion,
      precio: producto.precio,
      unidad_medida: producto.unidad_medida,
      activo: producto.activo,
    };
  };

  const handleAcceptDescription = async (codigo: string) => {
    const response = await fetch(
      ProductController.get.url({ query: { q: codigo, by: 'descripcion' } }),
    );
    const producto = (await response.json()) as Product;

    productItem = {
      id: producto.id,
      codigo: producto.codigo,
      cantidad: 1,
      descripcion: producto.descripcion,
      precio: producto.precio,
      unidad_medida: producto.unidad_medida,
      activo: producto.activo,
    };
  };
</script>

<fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
  <legend class="fieldset-legend px-2 leading-none">Producto</legend>

  <label>
    <span class="label">Descripcion</span>
    <!-- <textarea
      class="textarea min-h-0 h-14"
      bind:value={productoNuevo.descripcion}></textarea> -->
    <Autocomplete
      bind:value={productItem.descripcion}
      urlTemplate="/products/search?q=%s&by=descripcion"
      onAccept={handleAcceptDescription}
    />
  </label>

  <div class="flex flex-row gap-4">
    <label class="min-w-24">
      <span class="label">Codigo</span>
      <Autocomplete
        bind:value={productItem.codigo}
        urlTemplate="/products/search?q=%s"
        onAccept={handleAcceptCode}
      />
    </label>
    <label class="min-w-18">
      <span class="label">Cantidad</span>
      <input
        type="number"
        class="input"
        bind:value={productItem.cantidad}
        min="0"
      />
    </label>
  </div>

  <div
    class="grid grid-cols-[auto_1fr] gap-x-2 [&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right"
  >
    <span>Precio:</span>
    <span>{productItem.precio}</span>
    <span>Total:</span>
    <span>{productItem.precio * productItem.cantidad}</span>
    <span>U. Medida:</span>
    <span>{productItem.unidad_medida}</span>
  </div>

  <button
    class="btn btn-secondary mt-2"
    disabled={form.processing}
    type="button"
    onclick={addProduct}>Agregar Producto</button
  >
</fieldset>
