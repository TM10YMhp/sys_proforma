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
  import { MinusIcon, PlusIcon } from 'lucide-svelte';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import ProformaController from '@/actions/App/Http/Controllers/ProformaController';
  import Autocomplete from '@/components/_ui/autocomplete.svelte';
  import AppHead from '@/components/AppHead.svelte';
  import InputError from '@/components/InputError.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import { table } from '@/lib/variants';
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

    console.log(form);

    return

    // eslint-disable-next-line no-unreachable
    form.igv_tasa = porcentaje / 100;
    form.fecha_emision = datetimeToUTC(fecha_emision);
    form.fecha_vencimiento = datetimeToUTC(fecha_vencimiento);
    form.put(ProformaController.update.url(proforma.id));
  };

  const addProduct = (
    _: MouseEvent & {
      currentTarget: EventTarget & HTMLButtonElement;
    },
  ) => {
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
      <fieldset
        class="fieldset rounded-box border border-base-100 px-2 w-xs space-y-2"
      >
        <legend class="fieldset-legend px-2 leading-none mb-0">Proforma</legend>

        <div>
          <p>Codigo: <span class="font-bold">{form.codigo}</span></p>
          <InputError message={form.errors.codigo} />
          <p>IGV Monto: <span class="font-bold">{form.igv_monto}</span></p>
          <InputError message={form.errors.igv_monto} />
          <p>Total: <span class="font-bold">{form.total}</span></p>
          <InputError message={form.errors.total} />
        </div>

        <div class="flex flex-row gap-2">
          <div>
            <label for="emision" class="label">Fecha de Emision</label>
            <input
              id="emision"
              class="input"
              type="datetime-local"
              bind:value={fecha_emision}
            />
            <InputError message={form.errors.fecha_emision} />
          </div>
          <div>
            <label for="vencimiento" class="label">Fecha de Vencimiento</label>
            <input
              id="vencimiento"
              class="input"
              type="datetime-local"
              bind:value={fecha_vencimiento}
            />
            <InputError message={form.errors.fecha_vencimiento} />
          </div>
        </div>

        <div class="flex flex-row gap-2">
          <div>
            <label for="subtotal" class="label">Subtotal</label>
            <input
              id="subtotal"
              class="input"
              bind:value={form.subtotal}
              type="number"
              min="0"
              step="0.1"
              oninput={onChangeSubtotal}
            />
            <InputError message={form.errors.subtotal} />
          </div>
          <div>
            <label for="igv_tasa" class="label">IGV Tasa (%)</label>
            <input
              class="input"
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
      </fieldset>

      <fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
        <legend class="fieldset-legend px-2 leading-none">Producto</legend>

        <div>
          <label for="codigo" class="label">Codigo</label>
          <Autocomplete
            id="codigo"
            class="input"
            bind:value={productoNuevo.codigo}
            api="/products/search"
            onclick={onClickAutocomplete}
          />
          <!-- <Input id="nombre" bind:value={productoNuevo.nombre} /> -->
        </div>
        <div>
          <label for="descripcion" class="label">Descripcion</label>
          <textarea
            id="descripcion"
            class="textarea"
            bind:value={productoNuevo.descripcion}></textarea>
        </div>
        <div class="flex flex-row gap-4">
          <div>
            <label for="stock" class="label">Stock</label>
            <input
              id="stock"
              type="number"
              class="input"
              bind:value={productoNuevo.stock}
              min="0"
            />
          </div>
          <div>
            <label for="precio" class="label">Precio</label>
            <input
              id="precio"
              type="number"
              class="input"
              step="0.01"
              bind:value={productoNuevo.precio}
              min="0"
            />
          </div>
          <div>
            <label for="unidad_medida" class="label">Unidad de Medida</label>
            <input
              id="unidad_medida"
              class="input"
              bind:value={productoNuevo.unidad_medida}
            />
          </div>
        </div>
        <button
          class="btn btn-secondary mt-2"
          disabled={form.processing}
          type="button"
          onclick={addProduct}>Agregar Producto</button
        >
      </fieldset>

      <fieldset class="fieldset rounded-box border border-base-100 px-2 w-xs">
        <legend class="fieldset-legend px-2 leading-none">Cliente</legend>

        <label>
          <span class="label">Nombres</span>
          <input class="input" />
        </label>
        <div class="flex flex-row gap-4">
          <label>
            <span class="label">Primer Apellido</span>
            <input class="input" />
          </label>
          <label>
            <span class="label">Segundo Apellido</span>
            <input class="input" />
          </label>
        </div>
        <div class="flex flex-row gap-4">
          <label>
            <span class="label">RUC</span>
            <input type="number" class="input" />
          </label>
          <label>
            <span class="label">DNI</span>
            <input type="number" class="input" />
          </label>
          <label>
            <span class="label">Telefono</span>
            <input class="input" />
          </label>
        </div>

        <button
          class="btn btn-secondary mt-2"
          disabled={form.processing}
          type="button"
          onclick={(_) => {}}>Establecer Cliente</button
        >
      </fieldset>
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
            <th class="text-center">Stock</th>
            <th class="text-center">Activo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          {#each productos as item, idx (idx)}
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
              <td class="flex flex-row gap-1">
                <button
                  disabled={form.processing}
                  class="btn btn-square btn-info"
                  onclick={() => {}}
                >
                  <PlusIcon />
                </button>
                <button
                  disabled={form.processing}
                  class="btn btn-square btn-error"
                  onclick={() => {}}
                >
                  <MinusIcon />
                </button>
              </td>
            </tr>
          {/each}
        </tbody>
      </table>
    </div>

    <Button disabled={form.processing} type="submit">Actualizar Proforma</Button
    >
  </form>
</div>
