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

const schemaValidation = z.object({
  title: z.string().min(3),
  description: z.string().min(10),
  price: z.number(),
  category: z.array(z.number()),
  location: z.number(),
  status: z.string(),
  photos: z.array(z.number()),
});

export const AnnounceCreate = () => {
  const translate = useTranslate();
  const {
    getInputProps,
    saveButtonProps,
    setFieldValue,
    refineCore: { formLoading },
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
  const [files, setFiles] = useState<number[]>([]);
  const [isUploadLoading, setIsUploadLoading] = useState(false);

  const apiUrl = useApiUrl();
  const previews = <SimpleGrid cols={8}>
    {files.map((file, index) => {
      return <Card key={index} shadow="xs" style={{ display: "flex", margin: "5px", justifyContent: "space-between", alignItems: "center" }}>
        <Image key={index} src={`${apiUrl}/resources/${file}`} style={{ width: '50%' }} />
        <ActionIcon variant="default" onClick={() => handleRemove(index)}>
          <IconTrash color="red" size={16} />
        </ActionIcon>
      </Card>;
    })}
  </SimpleGrid>

  const handleRemove = (index: number) => {
    fetch(`${apiUrl}/resources/${files[index]}`, {
      method: "DELETE",
    });

    setFiles(files.filter((_, i) => i !== index));
  }

  const handleOnDrop = (files: FileWithPath[]) => {
    try {
      setIsUploadLoading(true);

      files.map(async (file) => {
        const formData = new FormData();
        formData.append("file", file);

        const res = await fetch(`${apiUrl}/resources`, {
          method: "POST",
          body: formData,
        });
        const json = await res.json();

        setFiles(
          (prev) => [...prev, json.data.id]
        );
      });

      setIsUploadLoading(false);
    } catch (error) {
      setIsUploadLoading(false);
    }
  };

  useEffect(() => {
    setFieldValue("photos", { set: files });
  }, [files]);

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
      <Dropzone
        accept={IMAGE_MIME_TYPE}
        onDrop={handleOnDrop}
        loading={isUploadLoading}
      >
        <Text align="center">Drop images here</Text>
      </Dropzone>
      {previews}
    </Create>
  );
};

