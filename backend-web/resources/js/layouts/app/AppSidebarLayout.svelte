<script lang="ts">
  import type { Snippet } from 'svelte';
  import AppContent from '@/components/AppContent.svelte';
  import AppShell from '@/components/AppShell.svelte';
  import AppSidebar from '@/components/AppSidebar.svelte';
  import AppSidebarHeader from '@/components/AppSidebarHeader.svelte';
  import { Toaster } from '@/components/ui/sonner';
  import type { BreadcrumbItem } from '@/types';

  let {
    breadcrumbs = [],
    children,
  }: {
    breadcrumbs?: BreadcrumbItem[];
    children?: Snippet;
  } = $props();
</script>

<AppShell variant="sidebar">
  <AppSidebar />
  <!-- NOTE: overflow rompe sticky, pero sin overflow se extiende el contenido -->
  <!-- HACK: el ancho fijo evita estirar el contenido -->
  <AppContent variant="sidebar" class="w-[50vw]">
    <AppSidebarHeader {breadcrumbs} />
    {@render children?.()}
  </AppContent>
  <Toaster />
</AppShell>
