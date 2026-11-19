<script module lang="ts">
  import proformasRoutes from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: proformasRoutes.index.url(),
      },
      {
        title: 'Editar',
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import { useZoomImageClick } from '@zoom-image/svelte';
  import { onMount } from 'svelte';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import AppHead from '@/components/AppHead.svelte';
  import type { ProductItem } from '@/types/product';
  import type { Proforma, ProformaForm } from '@/types/proforma';
  import SeccionCliente from './partials/SeccionCliente.svelte';
  import SeccionProducto from './partials/SeccionProducto.svelte';
  import SeccionProforma from './partials/SeccionProforma.svelte';
  import SeccionTabla from './partials/SeccionTabla.svelte';

  type Props = {
    proforma: Proforma;
  };
  let { proforma }: Props = $props();

  // svelte-ignore state_referenced_locally
  let productos = $state<ProductItem[]>(
    proforma.products.map((x) => ({
      id: x.id,
      codigo: x.codigo,
      descripcion: x.descripcion,
      precio: x.pivot.precio_unitario,
      unidad_medida: x.unidad_medida,
      cantidad: x.pivot.cantidad,
      activo: x.activo,
    })),
  );

  // svelte-ignore state_referenced_locally
  // svelte-ignore non_reactive_update
  let form = useForm<ProformaForm>({
    id: proforma.id,
    fecha_emision: proforma.fecha_emision,
    fecha_vencimiento: proforma.fecha_vencimiento,
    subtotal: proforma.subtotal,
    igv_tasa: proforma.igv_tasa,
    productos: proforma.products.map((x) => ({
      id: x.pivot.product_id,
      cantidad: x.pivot.cantidad,
    })),
    cliente_id: proforma.cliente_id,
  });

  // NOTE: $effect solo debe usarse de forma excepcional
  $effect(() => {
    form.subtotal = productos.reduce(
      (acum, producto) => acum + producto.cantidad * producto.precio,
      0,
    );
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();

    form.productos = productos.map((p) => ({
      id: p.id,
      cantidad: p.cantidad,
    }));

    form.put(ProformaController.update.url(proforma.id));
  };

  const addProduct = (productItem: ProductItem) => {
    const producto = productos.find((p) => p.id === productItem.id);

    if (producto) {
      producto.cantidad += productItem.cantidad;
    } else {
      productos.unshift({ ...productItem });
    }
  };

  const handleAcceptCliente = (cliente_id: number) => {
    form.cliente_id = cliente_id;
  };

  let imageContainer: HTMLDivElement;
  const { createZoomImage: createZoomImageClick } = useZoomImageClick();

  onMount(() => {
    createZoomImageClick(imageContainer);
  });
</script>

<AppHead title="Proformas | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="space-y-2">
    <div class="flex flex-row gap-2">
      <SeccionProforma bind:form {proforma} />

      <SeccionProducto {form} {productos} onAddProduct={addProduct} />

      <SeccionCliente {proforma} onAcceptCliente={handleAcceptCliente} />
    </div>

    <div class="flex flex-row gap-x-2 my-4">
      <button class="btn btn-primary" disabled={form.processing} type="submit"
        >Actualizar Proforma</button
      >

      <div class="divider divider-horizontal"></div>

      <a
        href={ProformaController.exportExcel.url(proforma.id)}
        class="btn [--btn-color:var(--color-green-900)]">Excel</a
      >
      <a
        href={ProformaController.exportPDF.url(proforma.id)}
        target="_blank"
        class="btn [--btn-color:var(--color-red-900)]">PDF</a
      >
    </div>

    <SeccionTabla {form} bind:productos />
  </form>

  <div
    class="relative w-[359.52px] h-[500px] cursor-crosshair overflow-hidden"
    bind:this={imageContainer}
  >
    <img
      class="w-full h-full"
      src="https://templates.invoicehome.com/factura-proforma-modelo-es-mono-negro-750px.png"
      alt=""
      loading="lazy"
    />
  </div>
</div>
