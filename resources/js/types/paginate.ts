// https://laravel-news.com/laravel-pagination#content-using-paginate-in-api-endpoints

export interface LaravelPaginationLink {
  url: string | null;
  label: string;
  active: boolean;
  page: number | null;
}

export interface LaravelPaginator<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: LaravelPaginationLink[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
}

export interface LaravelSimplePaginator<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number | null;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
}

