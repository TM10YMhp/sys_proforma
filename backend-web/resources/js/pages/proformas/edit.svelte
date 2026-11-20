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
  import { FileWarning } from 'lucide-svelte';
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

  // NOTE: el transpilador tiene problemas con "const enum"
  const ImageStatus = {
    Ok: 0,
    Error: 1,
    Pending: 2,
  } as const;
  type ImageStatus = (typeof ImageStatus)[keyof typeof ImageStatus];

  let imageStatus = $state.raw<ImageStatus>(ImageStatus.Pending);
  let imageUrl = $state.raw('');
  let isLoaded = $state.raw(false);

  let imageContainer: HTMLDivElement;
  const { createZoomImage: createZoomImageClick } = useZoomImageClick();

  onMount(async () => {
    const response = await fetch(ProformaController.getImgUrl.url(proforma.id));

    if (!response.ok) {
      imageStatus = ImageStatus.Error;

      return;
    }

    const data = (await response.json()) as { url: string };
    imageStatus = ImageStatus.Ok;
    imageUrl = data.url;
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

    <SeccionTabla {form} bind:productos />

    <div class="flex flex-row gap-x-2 mt-4">
      <button class="btn btn-primary" disabled={form.processing} type="submit"
        >Actualizar</button
      >

      <div class="divider divider-horizontal m-2"></div>

      <a
        href={ProformaController.getExcelById.url(proforma.codigo)}
        aria-disabled={!imageUrl}
        class="btn [--btn-color:var(--color-green-900)]">Excel</a
      >
      <a
        href={ProformaController.getPDFById.url(proforma.codigo)}
        aria-disabled={!imageUrl}
        target="_blank"
        class="btn [--btn-color:var(--color-red-900)]">PDF</a
      >
      <a
        href={ProformaController.getImgById.url(proforma.codigo)}
        aria-disabled={!imageUrl}
        download={`proforma_${proforma.codigo.replaceAll(' ', '_')}`}
        class="btn btn-info">Imagen</a
      >
    </div>
  </form>

  <!-- NOTE: es necesario especificar el alto y ancho para el zoom -->
  <div
    class="relative w-[359.52px] h-[500px] cursor-crosshair overflow-hidden"
    bind:this={imageContainer}
  >
    {#if !isLoaded}
      <div
        class="flex justify-center items-center gap-2 size-full bg-base-300 border rounded border-base-200"
      >
        {#if imageStatus === ImageStatus.Pending}
          <p class="loading loading-spinner text-info"></p>
          <p class="font-bold">Recuperando imagen</p>
        {:else if imageStatus === ImageStatus.Error}
          <FileWarning class="text-warning" />
          <p class="font-bold">No se pudo recuperar la imagen</p>
        {/if}
      </div>
    {/if}

    {#if imageUrl}
      <!-- NOTE: la imagen debe ser visible para el zoom -->
      <!-- class:hidden={!isLoaded} -->
      <img
        class="w-full h-full"
        src={imageUrl}
        alt={`proforma ${proforma.codigo}`}
        onload={() => {
          isLoaded = true;
          createZoomImageClick(imageContainer);
        }}
      />
    {/if}
  </div>
</div>
