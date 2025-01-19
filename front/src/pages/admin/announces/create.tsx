import { Create, useForm, useSelect } from "@refinedev/mantine";
import { useTranslate } from "@refinedev/core";
import { TextInput, NumberInput, Select, Text } from "@mantine/core";
import { z } from 'zod';
import { zodResolver } from "@mantine/form";
import { Upload } from "../../../components/upload";

const schemaValidation = z.object({
  title: z.string().min(3),
  description: z.string().min(10),
  price: z.number(),
  category: z.object({
   set: z.number().positive('Please select a category'),
  }),
  location: z.number(),
  status: z.string(),
  photos: z.object({
    set: z.array(z.number().positive()).nonempty('Please upload at least one photo'),
  }),
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
      category: { set: 0 },
      location: 10.000,
      status: "draft",
      photos: { set: [] },
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
      <Select
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
        error={(errors["photos.set"] || '') as string}
      />
    </Create>
  );
};

