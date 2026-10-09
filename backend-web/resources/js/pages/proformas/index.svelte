<script module lang="ts">
  import { index } from '@/routes/proformas';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Proformas',
        href: index.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import { Link, useForm } from '@inertiajs/svelte';
  import { useZoomImageClick } from '@zoom-image/svelte';
  import { onMount } from 'svelte';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import AppHead from '@/components/AppHead.svelte';
  import { cn } from '@/lib/utils';
  import { table } from '@/lib/variants';
  import type { LaravelPaginator } from '@/types/paginate';
  import type { Proforma } from '@/types/proforma';

  type Props = {
    proformas: LaravelPaginator<Proforma>;
  };
  let { proformas }: Props = $props();

  let showProducts = $state(!false);
  let selectedIndex = $state(0);

  const form = useForm();

  const handleDelete = (id: number) => {
    if (confirm('Estas seguro que deseas eliminar esta proforma?')) {
      form.delete(ProformaController.destroy.url(id));
    }
  };

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

  const formatPercent = (porcentaje: number) => {
    return porcentaje * 100 + '%';
  };
  const formatFixed = (numero: number) => {
    return numero.toFixed(2);
  };

  let dialog: HTMLDialogElement;

  let imageContainer: HTMLDivElement;
  const { createZoomImage: createZoomImageClick } = useZoomImageClick();

  onMount(() => {
    createZoomImageClick(imageContainer, {
      zoomImageSource: 'http://localhost:5138/api/proforma/img/1234',
    });
  });
</script>

<AppHead title="Proforma" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <Link href={ProformaController.create.url()} class="btn btn-primary w-fit">
    Nuevo Proforma
  </Link>

  <div class="overflow-x-auto">
    <table class={table()}>
      <thead>
        <tr>
          <th>#</th>
          <th>Codigo</th>
          <th class="text-center">Productos</th>
          <th>Fecha Emision</th>
          <th>Fecha Vencimiento</th>
          <th>Subtotal</th>
          <th>Tasa IGV</th>
          <th>Monto IGV</th>
          <th>Total</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        {#each proformas.data as item, index (item.id)}
          <tr
            class={[index == selectedIndex && 'bg-muted']}
            onclick={() => {
              // showProducts = !showProducts;
              selectedIndex = index;
            }}
          >
            <th>{index + Number(proformas.from)}</th>
            <td class="font-mono text-nowrap">{item.codigo}</td>
            <td class="text-center">{item.products.length}</td>
            <td class="font-mono text-nowrap"
              >{formatDate(item.fecha_emision)}</td
            >
            <td class="font-mono text-nowrap"
              >{formatDate(item.fecha_vencimiento)}</td
            >
            <td class="text-right font-mono">{item.subtotal}</td>
            <td class="text-right font-mono">{formatPercent(item.igv_tasa)}</td>
            <td class="text-right font-mono">{formatFixed(item.igv_monto)}</td>
            <td class="text-right font-mono">{item.total}</td>
            <td class="flex flex-row gap-1">
              <Link
                href={ProformaController.edit(item.id)}
                class="btn btn-info"
              >
                Editar
              </Link>
              <button
                disabled={form.processing}
                class="btn btn-error"
                onclick={() => handleDelete(item.id)}
              >
                Borrar
              </button>
              <a
                href={ProformaController.exportExcel.url(item.id)}
                class="btn [--btn-color:var(--color-green-900)]">Excel</a
              >
              <!-- HACK: decidir como gestionar la generacion de pdf -->
              <a
                href={ProformaController.exportPDF.url(
                  'bd8a4dfe-ff6f-4f54-9412-b522e4b3a235',
                )}
                target="_blank"
                class="btn [--btn-color:var(--color-red-900)]">PDF</a
              >
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </div>

  <div class="flex flex-row justify-between p-4">
    <span class="text-gray-400"
      >Mostrando <span class="font-semibold text-white"
        >{proformas.from}-{proformas.to}</span
      >
      de
      <span class="text-white">{proformas.total}</span></span
    >

    <div class="join">
      <Link
        prefetch="click"
        cacheFor="3m"
        href={proformas.prev_page_url!}
        class="join-item btn btn-soft">«</Link
      >
      {#each proformas.links as item (item.label)}
        {#if !item.label.includes('Previous') && !item.label.includes('Next')}
          <Link
            prefetch="click"
            cacheFor="3m"
            href={item.url!}
            class={cn('join-item btn btn-soft', item.active && 'btn-active')}
            >{item.label}</Link
          >
        {/if}
      {/each}
      <Link
        prefetch="click"
        cacheFor="3m"
        href={proformas.next_page_url!}
        class="join-item btn btn-soft">»</Link
      >
    </div>
  </div>

  <button class="btn" onclick={() => dialog.showModal()}>open modal</button>
  <dialog bind:this={dialog} class="modal modal-open">
    <div class="modal-box w-11/12 max-w-5xl">
      <p class="text-lg font-bold">
        Proforma {proformas.data[selectedIndex].codigo}
      </p>
      <div class="py-4 flex flex-row flex-wrap gap-2">
        <!-- NOTE: hay que establecer medidas de imagen -->
        <div
          class="relative w-[339.52px] h-[480px] cursor-crosshair overflow-hidden"
          bind:this={imageContainer}
        >
          <img
            class="w-full h-full"
            src="http://localhost:5138/api/proforma/img/1234"
            alt=""
          />
        </div>

        <div class="overflow-x-auto max-h-56">
          <table class={table() + ' table-pin-rows'}>
            <thead>
              <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th class="text-right">Precio</th>
                <th class="text-center">U. Medida</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Activo</th>
              </tr>
            </thead>
            <tbody>
              {const productos = $derived(
                proformas.data[selectedIndex].products,
              )}
              {#each productos as item, idx (item.id)}
                <tr>
                  <th>{idx + 1}</th>
                  <td class="font-mono">{item.codigo}</td>
                  <td class="truncate max-w-50">{item.descripcion}</td>
                  <td class="font-mono text-right">{item.precio}</td>
                  <td class="text-center">{item.unidad_medida}</td>
                  <td class="text-center">{item.stock}</td>
                  <td class="text-center">
                    {#if item.activo}
                      <span
                        class="inline-block bg-green-500 size-3 rounded-full"
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
      </div>
      <form method="dialog">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
          >✕</button
        >
      </form>
    </div>

    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>

  {#if showProducts}
    <p>
      Productos de la Proforma <span class="font-bold"
        >{proformas.data[selectedIndex].codigo}</span
      >
    </p>

    <div class="overflow-x-auto max-h-56">
      <table class={table() + ' table-pin-rows'}>
        <thead>
          <tr>
            <th>#</th>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th class="text-right">Precio</th>
            <th class="text-center">U. Medida</th>
            <th class="text-center">Stock</th>
            <th class="text-center">Activo</th>
          </tr>
        </thead>
        <tbody>
          {const productos = $derived(proformas.data[selectedIndex].products)}
          {#each productos as item, idx (item.id)}
            <tr>
              <th>{idx + 1}</th>
              <td class="font-mono">{item.codigo}</td>
              <td class="truncate max-w-50">{item.descripcion}</td>
              <td class="font-mono text-right">{item.precio}</td>
              <td class="text-center">{item.unidad_medida}</td>
              <td class="text-center">{item.stock}</td>
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
  {/if}
</div>
