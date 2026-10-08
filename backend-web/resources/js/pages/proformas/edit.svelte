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
  import { MinusIcon, PlusIcon, XIcon } from 'lucide-svelte';
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
  let info = $state(proforma);

  // svelte-ignore state_referenced_locally
  let porcentaje = $state(proforma.igv_tasa * 100);
  // svelte-ignore state_referenced_locally
  let productos = $state<
    (Omit<Product, 'stock' | 'created_at' | 'updated_at'> & {
      cantidad: number;
    })[]
  >(
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

  type Form = Omit<
    Proforma,
    'igv_monto' | 'total' | 'codigo' | 'created_at' | 'updated_at' | 'products'
  > & {
    products: {
      id: number;
      cantidad: number;
    }[];
  };
  // svelte-ignore state_referenced_locally
  const form = useForm<Form>({
    id: proforma.id,
    fecha_emision: proforma.fecha_emision,
    fecha_vencimiento: proforma.fecha_vencimiento,
    subtotal: proforma.subtotal,
    igv_tasa: proforma.igv_tasa,
    products: proforma.products.map((x) => ({
      id: x.pivot.product_id,
      cantidad: x.pivot.cantidad,
    })),
  });

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

    console.log(form.data());

    return;

    // eslint-disable-next-line no-unreachable
    form.igv_tasa = porcentaje / 100;
    form.fecha_emision = datetimeToUTC(fecha_emision);
    form.fecha_vencimiento = datetimeToUTC(fecha_vencimiento);
    form.put(ProformaController.update.url(proforma.id));
  };

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

  const obtenerSubtotal = () => {
    return productos.reduce(
      (acum, producto) => acum + producto.cantidad * producto.precio,
      0,
    );
  };

  const obtenerMontoIgv = () => {
    return obtenerSubtotal() * info.igv_tasa;
  };

  const obtenerTotal = () => {
    return obtenerMontoIgv() + obtenerSubtotal();
  };

  const incrementarCantidad = (id: number) => {
    const producto = productos.find((p) => p.id === id);

    if (producto) {
      producto.cantidad++;
    }
  };

  const decrementarCantidad = (id: number) => {
    const producto = productos.find((p) => p.id === id);

    if (!producto) {
      return;
    }

    if (producto.cantidad > 1) {
      producto.cantidad--;
    } else {
      productos = productos.filter((p) => p.id !== id);
    }
  };

  const eliminarProducto = (id: number) => {
    productos = productos.filter((p) => p.id !== id);
  };
</script>

<AppHead title="Proformas | Editar" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="space-y-4">
    <div class="flex flex-row gap-2">
      <fieldset
        class="fieldset rounded-box border border-base-100 px-2 w-xs space-y-2"
      >
        <legend class="fieldset-legend px-2 leading-none mb-0">Proforma</legend>

        <div
          class="grid grid-cols-[auto_1fr] gap-x-2 [&>*:nth-child(odd)]:font-bold [&>*:nth-child(odd)]:text-right"
        >
          <span>Codigo:</span>
          <span>{info.codigo}</span>
          <span>Subtotal:</span>
          <span>{obtenerSubtotal().toFixed(2)}</span>
          <span>IGV Monto:</span>
          <span>{obtenerMontoIgv().toFixed(2)}</span>
          <span>Total:</span>
          <span>{obtenerTotal().toFixed(2)}</span>
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

        <label for="igv_tasa" class="label">IGV Tasa (%)</label>
        <input
          class="input w-fit"
          id="igv_tasa"
          bind:value={porcentaje}
          type="number"
          min="0"
          max="100"
          step="0.1"
        />
        <InputError message={form.errors.igv_tasa} />
      </fieldset>

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
            class="input"
            bind:value={productoNuevo.codigo}
            api="/products/search"
            onclick={onClickAutocomplete}
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
            <th class="text-center">Cantidad</th>
            <th class="text-center">Total</th>
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
              <td class="text-center">{item.cantidad}</td>
              <td class="font-mono text-right"
                >{(item.precio * item.cantidad).toFixed(2)}</td
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
              <td class="flex flex-row gap-1">
                <button
                  disabled={form.processing}
                  class="btn btn-square btn-info"
                  onclick={() => incrementarCantidad(item.id)}
                >
                  <PlusIcon />
                </button>
                <button
                  disabled={form.processing}
                  class="btn btn-square btn-error"
                  onclick={() => decrementarCantidad(item.id)}
                >
                  <MinusIcon />
                </button>
                <div class="divider divider-horizontal mx-1"></div>
                <button
                  disabled={form.processing}
                  class="btn btn-square btn-error btn-soft"
                  onclick={() => eliminarProducto(item.id)}
                >
                  <XIcon />
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
