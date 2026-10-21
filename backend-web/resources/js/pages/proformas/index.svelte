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
  import { Link, router, useForm } from '@inertiajs/svelte';
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
              selectedIndex = index;
            }}
            ondblclick={(e) => {
              if ((e.target as HTMLElement).matches('a, button')) {
                return;
              }

              router.visit(ProformaController.show.url(item.id));
            }}
          >
            <th>{index + Number(proformas.from)}</th>
            <td class="font-mono text-nowrap"><span>{item.codigo}</span></td>
            <td class="text-center">
              {item.products.length}
            </td>
            <td class="font-mono text-nowrap"
              >{formatDate(item.fecha_emision)}</td
            >
            <td class="font-mono text-nowrap"
              >{formatDate(item.fecha_vencimiento)}</td
            >
            <td class="text-right font-mono">{formatFixed(item.subtotal)}</td>
            <td class="text-right font-mono">{formatPercent(item.igv_tasa)}</td>
            <td class="text-right font-mono">{formatFixed(item.igv_monto)}</td>
            <td class="text-right font-mono">{formatFixed(item.total)}</td>
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
              <a
                href={ProformaController.exportPDF.url(item.id)}
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
</div>
