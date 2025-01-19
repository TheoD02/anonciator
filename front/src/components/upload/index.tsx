import { SimpleGrid, Text } from "@mantine/core";
import {
  Dropzone,
  IMAGE_MIME_TYPE,
  type FileWithPath,
} from "@mantine/dropzone";
import { useApiUrl } from "@refinedev/core";
import { useState } from "react";
import { requestApi } from "../../api";
import { DropZoneImagePreview } from "../drop-zone-image-preview";

type UploadProps = {
  onChange?: (fileIds: number[]) => void;
  value?: number[];
  multiple?: boolean;
};

export const Upload = ({ onChange, value = [], multiple = true }: UploadProps) => {
  const [files, setFiles] = useState<number[]>(value);
  const [isUploadLoading, setIsUploadLoading] = useState(false);
  const apiUrl = useApiUrl();

  const handleRemove = (index: number) => {
    if (!files[index]) {
      console.error(files, index);
      console.error("File not found");
      return;
    }

    requestApi(`${apiUrl}/resources/${files[index]}`, {
      method: "DELETE",
    });

    const newFiles = files.filter((_, i) => i !== index);
    setFiles(newFiles);
    onChange?.(newFiles);
  };

  const handleOnDrop = async (droppedFiles: FileWithPath[]) => {
    try {
      setIsUploadLoading(true);

      const uploadPromises = droppedFiles.map(async (file) => {
        const formData = new FormData();
        formData.append("file", file);

        const res = await requestApi(`${apiUrl}/resources`, {
          method: "POST",
          body: formData,
        });
        const json = await res.json();
        return json.data.id;
      });

      const uploadedFileIds = await Promise.all(uploadPromises);
      const newFiles = multiple ? [...files, ...uploadedFileIds] : uploadedFileIds;
      
      setFiles(newFiles);
      onChange?.(newFiles);
    } catch (error) {
      console.error("Upload error:", error);
    } finally {
      setIsUploadLoading(false);
    }
  };

  const previews = (
    <SimpleGrid cols={8}>
      {files.map((file, index) => (
        <DropZoneImagePreview
          key={file}
          id={file}
          handleRemove={() => handleRemove(index)}
        />
      ))}
    </SimpleGrid>
  );

  return (
    <>
      <Dropzone
        accept={IMAGE_MIME_TYPE}
        onDrop={handleOnDrop}
        loading={isUploadLoading}
        multiple={multiple}
      >
        <Text align="center">Drop images here</Text>
      </Dropzone>
      {previews}
    </>
  );
}; 