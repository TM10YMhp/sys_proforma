<script lang="ts">
  import type { InertiaForm } from '@inertiajs/svelte';
  import Autocomplete from '@/components/_ui/autocomplete.svelte';
  import type { Product } from '@/types/product';
  import type { ProformaForm } from '@/types/proforma';

  let productoNuevo = $state<Omit<Product, 'id' | 'created_at' | 'updated_at'>>(
    {
      codigo: '',
      descripcion: '',
      precio: 0,
      unidad_medida: '',
      stock: 0,
      activo: true,
    },
  );

  // TODO: ahora debe enviarse a la base de datos
  const addProduct = (
    _: MouseEvent & {
      currentTarget: EventTarget & HTMLButtonElement;
    },
  ) => {
    // const draft = { ...productoNuevo };
    // productos.unshift(draft);
  };

  const onClickAutocomplete = async (_codigo: string) => {
    // let producto = productos.find((p) => p.codigo === codigo);
    // if (!producto) {
    //   const response = await fetch(ProductController.getByCode.url(codigo));
    //   producto = (await response.json()) as Product;
    // }
    // productoNuevo.descripcion = producto.descripcion;
    // productoNuevo.stock = producto.stock;
    // productoNuevo.precio = producto.precio;
    // productoNuevo.unidad_medida = producto.unidad_medida;
  };

  type Props = {
    form: InertiaForm<ProformaForm>;
  };
  let { form }: Props = $props();
</script>

<fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
  <legend class="fieldset-legend px-2 leading-none">Producto</legend>

  <div
    class="grid grid-cols-[auto_1fr] gap-x-2 [&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right"
  >
    <span>Stock:</span>
    <span>{productoNuevo.stock}</span>
  </div>

  <div>
    <label for="codigo" class="label">Codigo</label>
    <Autocomplete
      id="codigo"
      bind:value={productoNuevo.codigo}
      urlTemplate="/products/search?q=%s"
      onAccept={onClickAutocomplete}
    />
    <!-- <Input id="nombre" bind:value={productoNuevo.nombre} /> -->
  </div>
  <label>
    <span class="label">Descripcion</span>
    <textarea
      class="textarea min-h-0 h-14"
      bind:value={productoNuevo.descripcion}></textarea>
  </label>
  <div class="flex flex-row gap-4">
    <label>
      <span class="label">Cantidad</span>
      <input
        type="number"
        class="input"
        bind:value={productoNuevo.stock}
        min="0"
      />
    </label>
    <label>
      <span class="label">Precio</span>
      <input
        type="number"
        class="input"
        step="0.01"
        bind:value={productoNuevo.precio}
        min="0"
      />
    </label>
    <label>
      <span class="label">U. Medida</span>
      <input class="input" bind:value={productoNuevo.unidad_medida} />
    </label>
  </div>
  <button
    class="btn btn-secondary mt-2"
    disabled={form.processing}
    type="button"
    onclick={addProduct}>Agregar Producto</button
  >
</fieldset>
