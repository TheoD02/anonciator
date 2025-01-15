import { CreateManyParams, CreateParams, CustomParams, DataProvider, DeleteManyParams, DeleteOneParams, GetListParams, GetManyParams, GetOneParams, UpdateManyParams, UpdateParams } from "@refinedev/core";
import { TOKEN_KEY } from "./authProvider";

const defaultHeaders = {
  "Content-Type": "application/json",
  "Accept": "application/json",
};

export const requestApi = async (url: string, options?: RequestInit) => {
  const token = localStorage.getItem(TOKEN_KEY);

  if (!options) {
    options = {};
  }

  if (token) {
    options.headers = {
      ...defaultHeaders,
      'Authorization': `Bearer ${token}`,
      ...options.headers,
    };
  }

  if (options.body && options.body instanceof FormData) {
      delete options.headers['Content-Type'];
  }

  const response = await fetch(url, options);
  return response;
}

export const apiDataProvider: DataProvider = (url: string) => ({
  getList: async ({ resource, pagination, sorters, filters, meta }: GetListParams) => {
    if (!pagination) {
      pagination = {
        current: 1,
        pageSize: 10,
      };
    }
    const computedUrl = new URL(`${url}/${resource}`);
    computedUrl.searchParams.append("page", pagination.current.toString());
    computedUrl.searchParams.append("limit", pagination.pageSize.toString());

    if (sorters) {
      for (const sorter of sorters) {
        computedUrl.searchParams.append("sort", `${sorter.field}:${sorter.direction}`);
      }
    }

    if (filters) {
      for (const filter of filters) {
        if (filter.value === '') {
          continue;
        }
        computedUrl.searchParams.append(`${filter.field}[${filter.operator}]`, filter.value);
      }
    }

    const data = await (await requestApi(computedUrl.toString())).json();

    return {
      data: data.data,
      total: data.meta.totalItems,
      meta: data.meta,
    };
  },

  getOne: async ({ resource, id, meta }: GetOneParams) => {
    const data = await (await requestApi(`${url}/${resource}/${id}`)).json();

    return {
      data: data.data,
      meta: data.meta,
    };
  },

  getMany: async ({ resource, ids, meta }: GetManyParams) => {
    const data = await (await requestApi(`${url}/${resource}?id[in]=${[...new Set(ids)].join(",")}`)).json();

    return {
      data: data.data,
      meta: data.meta,
    };
  },

  create: async ({ resource, variables, meta }: CreateParams) => {
    const data = await (await requestApi(`${url}/${resource}`, {
      method: "POST",
      body: JSON.stringify(variables),
    })).json();

    return {
      data: data.data,
      meta: data.meta,
    }
  },
  update: async ({ resource, id, variables, meta }: UpdateParams) => {
    const data = await (await requestApi(`${url}/${resource}/${id}`, {
      method: "PUT",
      body: JSON.stringify(variables),
    })).json();

    return {
      data: data.data,
      meta: data.meta,
    };
  },
  deleteOne: async ({ resource, id, variables, meta }: DeleteOneParams) => {
    const response = await requestApi(`${url}/${resource}/${id}`, {
      method: "DELETE",
    });
    const data = await response.text();

    if (response.status === 404) {
      // Silent error we don't want to show to the user (is ok if the record is already deleted)
      return;
    }

    if (response.status === 204) {
      // Silent success
      return;
    }

    throw new Error(data);
  },
  createMany: ({ resource, variables, meta }: CreateManyParams) => {
    throw new Error("Not implemented");
  },
  deleteMany: ({ resource, ids, variables, meta }: DeleteManyParams) => {
    throw new Error("Not implemented");
  },
  updateMany: ({ resource, ids, variables, meta }: UpdateManyParams) => {
    throw new Error("Not implemented");
  },
  custom: ({ url, method, filters, sorters, payload, query, headers, meta }: CustomParams) => {
    throw new Error("Not implemented");
  },
  getApiUrl: () => url,
});

