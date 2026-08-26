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
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
  } from '@/components/_ui/pagination';
  import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
  } from '@/components/_ui/table';
  import AppHead from '@/components/AppHead.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import type { LaravelPaginator } from '@/types/paginate';
  import type { Proforma } from '@/types/proforma';

  type Props = {
    proformas: LaravelPaginator<Proforma>;
  };
  let { proformas }: Props = $props();

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
  <!-- TODO: el cursor no se establece -->
  <Link href={ProformaController.create.url()} class="w-fit">
    <Button>Nueva Proforma</Button>
  </Link>

  <Table>
    <TableHeader>
      <TableRow>
        <TableHead>Codigo</TableHead>
        <TableHead>Fecha Emision</TableHead>
        <TableHead>Fecha Vencimiento</TableHead>
        <TableHead>Subtotal</TableHead>
        <TableHead>Tasa IGV</TableHead>
        <TableHead>Monto IGV</TableHead>
        <TableHead>Total</TableHead>
        <TableHead>Acciones</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      {#each proformas.data as item (item.id)}
        <TableRow>
          <TableCell>{item.codigo}</TableCell>
          <TableCell class="font-mono"
            >{formatDate(item.fecha_emision)}</TableCell
          >
          <TableCell class="font-mono"
            >{formatDate(item.fecha_vencimiento)}</TableCell
          >
          <TableCell>{item.subtotal}</TableCell>
          <TableCell>{formatPercent(item.igv_tasa)}</TableCell>
          <TableCell>{formatFixed(item.igv_monto)}</TableCell>
          <TableCell>{item.total}</TableCell>
          <TableCell>
            <Link href={ProformaController.edit(item.id)}>
              <Button class="bg-slate-500 hover:bg-slate-700">Editar</Button>
            </Link>
            <Button
              disabled={form.processing}
              class="bg-red-500 hover:bg-red-700"
              onclick={() => handleDelete(item.id)}
            >
              Borrar
            </Button>
          </TableCell>
        </TableRow>
      {/each}
    </TableBody>
  </Table>

  <div class="flex flex-row justify-between p-4">
    <span class="text-gray-400"
      >Mostrando <span class="font-semibold text-white"
        >{proformas.from}-{proformas.to}</span
      >
      de
      <span class="text-white">{proformas.total}</span></span
    >
    <Pagination class="mx-0 w-auto">
      <PaginationContent>
        <PaginationItem>
          <PaginationPrevious
            class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            href={proformas.prev_page_url}
          />
        </PaginationItem>
        {#each proformas.links as item (item.label)}
          {#if !item.label.includes('Previous') && !item.label.includes('Next')}
            <PaginationItem>
              <PaginationLink
                href={item.url}
                class={`flex items-center justify-center px-3 py-2 text-sm leading-tight border ${item.active ? 'hover:bg-primary-100 hover:text-primary-700 border-gray-700 bg-gray-700 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700 hover:text-white'}`}
                >{item.label}</PaginationLink
              >
            </PaginationItem>
          {/if}
        {/each}
        <PaginationItem>
          <PaginationNext
            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            href={proformas.next_page_url}
          />
        </PaginationItem>
      </PaginationContent>
    </Pagination>
  </div>
</div>
