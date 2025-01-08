import { CreateManyParams, CreateParams, CustomParams, DataProvider, DeleteManyParams, DeleteOneParams, GetListParams, GetManyParams, GetOneParams, UpdateManyParams, UpdateParams } from "@refinedev/core";

const defaultHeaders = {
  "Content-Type": "application/json",
  "Accept": "application/json",
};

export const apiDataProvider: DataProvider = (url: string) => ({
  getList: async ({ resource, pagination, sorters, filters, meta }: GetListParams) => {
    if (!pagination) {
      pagination = {
        current: 1,
        pageSize: 10,
      };
    }

    const response = await fetch(`${url}/${resource}?page=${pagination.current}&limit=${pagination.pageSize}`, {
      headers: defaultHeaders,
    });
    const data = await response.json();

    return {
      data: data.data,
      total: data.meta.totalItems,
      meta: data.meta,
    };
  },

  getOne: async ({resource, id, meta}: GetOneParams) => {
    const response = await fetch(`${url}/${resource}/${id}`, {
      headers: defaultHeaders,
    });
    const data = await response.json();

    return {
      data: data.data,
      meta: data.meta,
    };
  },

  getMany: async ({resource, ids, meta}: GetManyParams) => {
    const response = await fetch(`${url}/${resource}?id[in]=${[...new Set(ids)].join(",")}`, {
      headers: defaultHeaders,
    });
    const data = await response.json();

    return {
      data: data.data,
      meta: data.meta,
    };
  },

  create: async ({resource, variables, meta}: CreateParams) => {
    const response = await fetch(`${url}/${resource}`, {
      method: "POST",
      body: JSON.stringify(variables),
      headers: defaultHeaders,
    });
    const data = await response.json();

    return {
      data: data.data,
      meta: data.meta,
    }
  },
  update: async ({resource, id, variables, meta}: UpdateParams) => {
    const response = await fetch(`${url}/${resource}/${id}`, {
      method: "PUT",
      body: JSON.stringify(variables),
      headers: defaultHeaders
    });
    const data = await response.json();

    return {
      data: data.data,
      meta: data.meta,
    };
  },
  deleteOne: async ({resource, id, variables, meta}: DeleteOneParams) => {
    const response = await fetch(`${url}/${resource}/${id}`, {
      method: "DELETE",
      headers: defaultHeaders,
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
  createMany: ({resource, variables, meta}: CreateManyParams) => {
    throw new Error("Not implemented");
  },
  deleteMany: ({resource, ids, variables, meta}:DeleteManyParams) => {
    throw new Error("Not implemented");
  },
  updateMany: ({resource, ids, variables, meta}: UpdateManyParams) => {
    throw new Error("Not implemented");
  },
  custom: ({url, method, filters, sorters, payload, query, headers, meta}: CustomParams) => {
    throw new Error("Not implemented");
  },
  getApiUrl: () => url,
});

