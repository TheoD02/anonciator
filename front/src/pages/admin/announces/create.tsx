import { Create, useForm, useSelect } from "@refinedev/mantine";
import { useApiUrl, useTranslate } from "@refinedev/core";
import { TextInput, NumberInput, Select, MultiSelect, Image, Text, Card, ActionIcon, Flex, SimpleGrid } from "@mantine/core";
import {
  Dropzone,
  IMAGE_MIME_TYPE,
  type FileWithPath,
} from "@mantine/dropzone";
import { useEffect, useState } from "react";
import { IconTrash } from "@tabler/icons-react";
import { z } from 'zod';
import { zodResolver } from "@mantine/form";
import { requestApi } from "../../../api";
import { DropZoneImagePreview } from "../../../components/drop-zone-image-preview";
import { Upload } from "../../../components/upload";

const schemaValidation = z.object({
  title: z.string().min(3),
  description: z.string().min(10),
  price: z.number(),
  // Skip category for now (Front use Single value, and backend use array, need to improve this)
  //category: z.object({
  //  set: z.number(),
  //}),
  location: z.number(),
  status: z.string(),
  //photos: z.array(z.object({
  //  set: z.array(z.number()),
  //})).min(0),
});

export const AnnounceCreate = () => {
  const translate = useTranslate();
  const {
    getInputProps,
    saveButtonProps,
    setFieldValue,
    refineCore: { formLoading },
    values,
    errors,
  } = useForm({
    initialValues: {
      title: "Cooul",
      description: "Woaw",
      price: 10.000,
      category: { set: [1] },
      location: 10.000,
      status: "draft",
    },
    validate: zodResolver(schemaValidation),
  });

  const { selectProps: categorySelectProps } = useSelect({
    resource: "announces/categories",
    optionLabel: "name",
    optionValue: "id",
    defaultValue: [],
  });

  return (
    <Create isLoading={formLoading} saveButtonProps={saveButtonProps}>
      <TextInput
        mt="sm"
        label={translate("announces.fields.title")}
        {...getInputProps("title")}
      />
      <TextInput
        mt="sm"
        label={translate("announces.fields.description")}
        {...getInputProps("description")}
      />
      <NumberInput
        mt="sm"
        label={translate("announces.fields.price")}
        {...getInputProps("price")}
      />
      <MultiSelect // We should use Select here but need to transform single value to array (or handle that in backend)
        maxSelectedValues={1}
        mt="sm"
        label={translate("announces.fields.categoryId")}
        {...getInputProps("category.set")}
        {...categorySelectProps}
      />
      <NumberInput
        mt="sm"
        label={translate("announces.fields.location")}
        {...getInputProps("location")}
      />
      <Select
        mt="sm"
        label={translate("announces.fields.status")}
        data={[
          { value: 'published', label: 'Published' },
          { value: 'draft', label: 'Draft' },
        ]}
        {...getInputProps("status")}
      />
      <Text mt="sm">Photos</Text>
      <Upload
        onChange={(files) => setFieldValue("photos", { set: files })}
        value={values.photos?.set}
      />
    </Create>
  );
};

