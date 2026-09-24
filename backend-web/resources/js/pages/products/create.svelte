<script module lang="ts">
  import productRoutes from '@/routes/products';

  export const layout = {
    breadcrumbs: [
      {
        title: 'Productos',
        href: productRoutes.index.url(),
      },
      {
        title: 'Nuevo',
        href: productRoutes.create.url(),
      },
    ],
  };
</script>

<script lang="ts">
  import { useForm } from '@inertiajs/svelte';
  import ProductController from '@/actions/App/Http/Controllers/ProductController';
  import Textarea from '@/components/_ui/textarea.svelte';
  import AppHead from '@/components/AppHead.svelte';
  import InputError from '@/components/InputError.svelte';
  import Button from '@/components/ui/button/Button.svelte';
  import Input from '@/components/ui/input/Input.svelte';
  import { Label } from '@/components/ui/label';
  import type { Product } from '@/types/product';

  const { codigo } = $props();

  // svelte-ignore state_referenced_locally
  const form = useForm<Omit<Product, 'id' | 'created_at' | 'updated_at'>>({
    codigo,
    descripcion: '',
    stock: 0,
    precio: 0,
    unidad_medida: '',
    activo: true,
  });

  const handleSubmit = (e: SubmitEvent) => {
    e.preventDefault();
    form.post(ProductController.store.url());
  };
</script>

<AppHead title="Productos | Nuevo" />

<div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
  <form onsubmit={handleSubmit} class="w-1/3 space-y-4">
    <div>
      <p>Codigo: <span class="font-bold">{codigo}</span></p>
      <InputError message={form.errors.codigo} />
    </div>
    <div>
      <Label for="descripcion">Descripcion</Label>
      <Textarea id="descripcion" bind:value={form.descripcion}></Textarea>
      <InputError message={form.errors.descripcion} />
    </div>
    <div class="flex flex-row gap-4">
      <div>
        <Label for="stock">Stock</Label>
        <Input id="stock" type="number" bind:value={form.stock} />
        <InputError message={form.errors.stock} />
      </div>
      <div>
        <Label for="precio">Precio</Label>
        <Input id="precio" type="number" step="0.01" bind:value={form.precio} />
        <InputError message={form.errors.precio} />
      </div>
      <div>
        <Label for="unidad_medida">Unidad de Medida</Label>
        <Input id="unidad_medida" bind:value={form.unidad_medida} />
        <InputError message={form.errors.unidad_medida} />
      </div>
    </div>
    <Button disabled={form.processing} type="submit">Crear Producto</Button>
  </form>
</div>
