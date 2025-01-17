import { ActionIcon, Card, Image } from "@mantine/core";
import { IconTrash } from "@tabler/icons-react";
import { requestApi } from "../api";
import { useApiUrl } from "@refinedev/core";
import { useEffect, useState } from "react";

type DropZoneImagePreviewProps = {
    id: number;
    height?: number;
    handleRemove?: () => void;
};

export const DropZoneImagePreview = ({ id , handleRemove }: DropZoneImagePreviewProps) => {
    const apiUrl = useApiUrl();
    const url = `${apiUrl}/resources/${id}`;
    const [imageObjectURL, setImageObjectURL] = useState<string | null>(null);
    const fetchImage = async () => {
        requestApi(url).then((res) => res.blob()).then((blob) => {
            const objectURL = URL.createObjectURL(blob);
            setImageObjectURL(objectURL);
        });
    }

    useEffect(() => {
        fetchImage();
    }, [id]);

    return <Card shadow="xs" style={{ display: "flex", margin: "5px", justifyContent: "space-between", alignItems: "center" }}>
        <Image src={imageObjectURL} style={{ width: '50%' }} />
        <ActionIcon variant="default" onClick={handleRemove}>
            <IconTrash color="red" size={16} />
        </ActionIcon>
    </Card>;
}