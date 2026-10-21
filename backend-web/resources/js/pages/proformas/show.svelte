<script module lang="ts">
  import { index } from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: index.url(),
      },
      {
        title: 'Mostrar',
      },
    ],
  };
</script>

<script lang="ts">
  import { Link } from '@inertiajs/svelte';
  import { useZoomImageClick } from '@zoom-image/svelte';
  import { onMount } from 'svelte';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import AppHead from '@/components/AppHead.svelte';
  import { table } from '@/lib/variants';
  import type { Proforma } from '@/types/proforma';

  type Props = {
    proforma: Proforma;
  };
  const { proforma }: Props = $props();

  let imageContainer: HTMLDivElement;
  const { createZoomImage: createZoomImageClick } = useZoomImageClick();

  onMount(() => {
    createZoomImageClick(imageContainer);
  });

  const formatDate = (fecha_utc: string) => {
    const date = new Date(fecha_utc);

    const formato = new Intl.DateTimeFormat('es-PE', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
    }).format(date);

    return formato
      .replaceAll('/', '-')
      .replace(',', '')
      .replace('a. m.', 'AM')
      .replace('p. m.', 'PM');
  };
</script>

<AppHead title={`Proforma | ${proforma.codigo}`} />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <div
    class={[
      'h-fit',
      'grid grid-cols-[auto_1fr] gap-x-2',
      '[&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right',
      '[&>*:nth-child(even)]:font-mono',
    ]}
  >
    <span>Codigo:</span>
    <span>{proforma.codigo}</span>
    <span>Fecha Emision:</span>
    <span>{formatDate(proforma.fecha_emision)}</span>
    <span>Fecha Vencimiento:</span>
    <span>{formatDate(proforma.fecha_vencimiento)}</span>
    <span>Subtotal:</span>
    <span>{proforma.subtotal.toFixed(2)}</span>
    <span>IGV Monto:</span>
    <span>{proforma.igv_monto.toFixed(2)}</span>
    <span>Total:</span>
    <span>{proforma.total.toFixed(2)}</span>
  </div>

  <div class="overflow-x-auto">
    <table class={table()}>
      <thead>
        <tr>
          <th>#</th>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th class="text-right">Precio</th>
          <th class="text-center">U. Medida</th>
          <th class="text-center">Cantidad</th>
          <th class="text-center">Total</th>
          <th class="text-center">Activo</th>
        </tr>
      </thead>
      <tbody>
        {#each proforma.products as item, idx (idx)}
          <tr>
            <th>{idx + 1}</th>
            <td class="font-mono">{item.codigo}</td>
            <td class="truncate max-w-50">{item.descripcion}</td>
            <td class="font-mono text-right">{item.precio}</td>
            <td class="text-center">{item.unidad_medida}</td>
            <td class="text-center">{item.pivot.cantidad}</td>
            <td class="font-mono text-right"
              >{(item.precio * item.pivot.cantidad).toFixed(2)}</td
            >
            <td class="text-center">
              {#if item.activo}
                <span class="inline-block bg-green-500 size-3 rounded-full"
                ></span>
              {:else}
                <span class="inline-block bg-red-500 size-3 rounded-full"
                ></span>
              {/if}
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>

  <div class="flex flex-row gap-x-2">
    <Link href={ProformaController.edit(proforma.id)} class="btn btn-info">
      Editar
    </Link>
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
