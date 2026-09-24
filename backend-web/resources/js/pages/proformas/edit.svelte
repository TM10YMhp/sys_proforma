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
        href: proformasRoutes.edit.url(0),
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import type { MouseEventHandler } from 'svelte/elements';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import Autocomplete from '@/components/_ui/autocomplete.svelte';
  import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
  } from '@/components/_ui/table';
  import Textarea from '@/components/_ui/textarea.svelte';
  import AppHead from '@/components/AppHead.svelte';
  import InputError from '@/components/InputError.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import Input from '@/components/ui/input/Input.svelte';
  import { Label } from '@/components/ui/label';
  import type { Product } from '@/types/product';
  import type { Proforma } from '@/types/proforma';

  type Props = {
    proforma: Proforma;
  };
  let { proforma }: Props = $props();

  // svelte-ignore state_referenced_locally
  let porcentaje = $state(proforma.igv_tasa * 100);
  // svelte-ignore state_referenced_locally
  let productos = $state<Omit<Product, 'id' | 'created_at' | 'updated_at'>[]>(
    proforma.products,
  );

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

  // svelte-ignore state_referenced_locally
  const form = useForm<Omit<Proforma, 'id' | 'created_at' | 'updated_at'>>({
    codigo: proforma.codigo,
    fecha_emision: proforma.fecha_emision,
    fecha_vencimiento: proforma.fecha_vencimiento,
    subtotal: proforma.subtotal,
    igv_tasa: proforma.igv_tasa,
    igv_monto: proforma.igv_monto,
    total: proforma.total,
    products: proforma.products,
  });

  const onChangeSubtotal = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const value = target.valueAsNumber;
    // form.subtotal = value;
    form.igv_monto = value * form.igv_tasa;
    form.total = value + form.igv_monto;
  };

  const stringToDatetime = (fecha_utc: string) => {
    const dateLocal = new Date(fecha_utc);
    const offset = dateLocal.getTimezoneOffset() * 60000;
    const localISODate = new Date(dateLocal.getTime() - offset).toISOString();

    return localISODate.slice(0, 16);
  };

  // TODO: solo es necesario como valor inicial
  let fecha_emision = $state(stringToDatetime(form.fecha_emision));
  let fecha_vencimiento = $state(stringToDatetime(form.fecha_vencimiento));

  const datetimeToUTC = (datetime: string) => {
    return new Date(datetime).toISOString();
  };

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.igv_tasa = porcentaje / 100;
    form.fecha_emision = datetimeToUTC(fecha_emision);
    form.fecha_vencimiento = datetimeToUTC(fecha_vencimiento);
    form.put(ProformaController.update.url(proforma.id));
  };

  const addProduct = (_: MouseEventHandler<HTMLButtonElement>) => {
    const draft = { ...productoNuevo };
    productos.unshift(draft);
  };

  const onClickAutocomplete = async (codigo: string) => {
    let producto = productos.find((p) => p.codigo === codigo);

    if (!producto) {
      const response = await fetch(ProductController.getByCode.url(codigo));
      producto = (await response.json()) as Product;
    }

    productoNuevo.descripcion = producto.descripcion;
    productoNuevo.stock = producto.stock;
    productoNuevo.precio = producto.precio;
    productoNuevo.unidad_medida = producto.unidad_medida;
  };
</script>

<AppHead title="Proformas | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="space-y-4">
    <div class="flex flex-row gap-6">
      <div class="space-y-4">
        <div>
          <p>Codigo: <span class="font-bold">{form.codigo}</span></p>
          <InputError message={form.errors.codigo} />
        </div>
        <div class="flex flex-row gap-2">
          <div>
            <Label for="emision">Fecha de Emision</Label>
            <Input
              id="emision"
              type="datetime-local"
              bind:value={fecha_emision}
            />
            <InputError message={form.errors.fecha_emision} />
          </div>
          <div>
            <Label for="vencimiento">Fecha de Vencimiento</Label>
            <Input
              id="vencimiento"
              type="datetime-local"
              bind:value={fecha_vencimiento}
            />
            <InputError message={form.errors.fecha_vencimiento} />
          </div>
        </div>
        <div class="flex flex-row gap-2">
          <div>
            <Label for="subtotal">Subtotal</Label>
            <Input
              id="subtotal"
              bind:value={form.subtotal}
              type="number"
              min="0"
              step="0.1"
              oninput={onChangeSubtotal}
            />
            <InputError message={form.errors.subtotal} />
          </div>
          <div>
            <Label for="igv_tasa">IGV Tasa (%)</Label>
            <!-- TODO: el ancho debe establecerse -->
            <Input
              class="w-fit"
              id="igv_tasa"
              bind:value={porcentaje}
              type="number"
              min="0"
              max="100"
              step="0.1"
            />
            <InputError message={form.errors.igv_tasa} />
          </div>
        </div>
        <div class="flex flex-row gap-2">
          <div>
            <Label for="igv_monto">IGV Monto</Label>
            <Input id="igv_monto" bind:value={form.igv_monto} readonly />
            <InputError message={form.errors.igv_monto} />
          </div>
          <div>
            <Label for="total">Total</Label>
            <Input id="total" bind:value={form.total} readonly />
            <InputError message={form.errors.total} />
          </div>
        </div>
      </div>
      <div class="bg-stone-900 p-2 rounded">
        <div>
          <Label for="codigo">Codigo</Label>
          <Autocomplete
            id="codigo"
            bind:value={productoNuevo.codigo}
            api="/products/search"
            onclick={onClickAutocomplete}
          />
          <!-- <Input id="nombre" bind:value={productoNuevo.nombre} /> -->
        </div>
        <div>
          <Label for="descripcion">Descripcion</Label>
          <Textarea id="descripcion" bind:value={productoNuevo.descripcion}
          ></Textarea>
        </div>
        <div class="flex flex-row gap-4">
          <div>
            <Label for="stock">Stock</Label>
            <Input id="stock" type="number" bind:value={productoNuevo.stock} />
          </div>
          <div>
            <Label for="precio">Precio</Label>
            <Input
              id="precio"
              type="number"
              step="0.01"
              bind:value={productoNuevo.precio}
            />
          </div>
          <div>
            <Label for="unidad_medida">Unidad de Medida</Label>
            <Input
              id="unidad_medida"
              bind:value={productoNuevo.unidad_medida}
            />
          </div>
        </div>
        <div class="flex flex-row justify-center mt-2">
          <Button disabled={form.processing} type="button" onclick={addProduct}
            >Agregar Producto</Button
          >
        </div>
      </div>
    </div>
    <Table>
      <TableHeader class="sticky top-0 bg-background">
        <TableRow>
          <TableHead>#</TableHead>
          <TableHead>Codigo</TableHead>
          <TableHead>Descripcion</TableHead>
          <TableHead>Precio</TableHead>
          <TableHead>U. Medida</TableHead>
          <TableHead>Stock</TableHead>
          <TableHead class="text-center">Activo</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {#each productos as item, idx (idx)}
          <TableRow>
            <TableCell>{idx + 1}</TableCell>
            <TableCell>{item.codigo}</TableCell>
            <TableCell>{item.descripcion}</TableCell>
            <TableCell>{item.precio}</TableCell>
            <TableCell>{item.unidad_medida}</TableCell>
            <TableCell>{item.stock}</TableCell>
            <TableCell class="text-center">
              {#if item.activo}
                <span class="inline-block bg-green-500 size-3 rounded-full"
                ></span>
              {:else}
                <span class="inline-block bg-red-500 size-3 rounded-full"
                ></span>
              {/if}
            </TableCell>
          </TableRow>
        {/each}
      </TableBody>
    </Table>
    <Button disabled={form.processing} type="submit">Actualizar Proforma</Button
    >
  </form>
</div>
