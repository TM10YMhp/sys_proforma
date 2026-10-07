import { cva } from '@lynstack/class-recipe';

export const table = cva({
  base: 'table rounded-box border border-base-content/5 [&_:not(thead,tfoot)_tr]:hover:bg-base-300',
  variants: {
    compact: {
      true: 'table-xs [&_:not(thead,tfoot)_tr]:text-sm',
    },
  },
  defaultVariants: { compact: true },
});
