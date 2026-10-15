<script lang="ts">
  import type { InertiaForm } from '@inertiajs/svelte';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import Autocomplete from '@/components/_ui/autocomplete.svelte';
  import type { Product } from '@/types/product';
  import type { ProformaForm } from '@/types/proforma';

  type ProductItem = {
    id: number;
    codigo: string;
    descripcion: string;
    precio: number;
    unidad_medida: string;
    cantidad: number;
    activo: boolean;
  };

  let productItem = $state<ProductItem>({
    id: 0,
    codigo: '',
    cantidad: 0,
    descripcion: '',
    precio: 0,
    unidad_medida: '',
    activo: true,
  });

  // TODO: ahora debe enviarse a la base de datos
  const addProduct = (
    _: MouseEvent & {
      currentTarget: EventTarget & HTMLButtonElement;
    },
  ) => {
    if (productItem.cantidad <= 0) {
      alert('Debe ingresar una cantidad mayor a 0');

      return;
    }

    // const draft = { ...productoNuevo };
    // productos.unshift(draft);
    console.log(productItem);
  };

  const handleAcceptCode = async (codigo: string) => {
    const response = await fetch(ProductController.getByCode.url(codigo));
    const producto = (await response.json()) as Product;

    productItem = {
      id: producto.id,
      codigo: producto.codigo,
      cantidad: 0,
      descripcion: producto.descripcion,
      precio: producto.precio,
      unidad_medida: producto.unidad_medida,
      activo: producto.activo,
    };
  };

  type Props = {
    form: InertiaForm<ProformaForm>;
  };
  let { form }: Props = $props();
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
    <label>
      <span class="label">Cantidad</span>
      <input
        type="number"
        class="input"
        bind:value={productItem.cantidad}
        min="0"
      />
    </label>
    <label>
      <span class="label">Precio</span>
      <input
        type="number"
        class="input"
        step="0.1"
        bind:value={productItem.precio}
        min="0"
      />
    </label>
  </div>

  <div
    class="grid grid-cols-[auto_1fr] gap-x-2 [&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right"
  >
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
