import { Badge, Button, Card, Center, Divider, Flex, Grid, Group, Image, Loader, LoadingOverlay, Pagination, Select, Text, TextInput, Title, Box, RangeSlider, Stack, Paper } from "@mantine/core";
import { BaseKey, useApiUrl, useList, useMany } from "@refinedev/core";
import { Carousel } from '@mantine/carousel';
import { useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useDebouncedState, useDebouncedValue } from '@mantine/hooks';
import { requestApi } from "../../../api";
import { IconMoodEmpty } from "@tabler/icons-react";

type ImagePreviewProps = {
    id: number;
    height: number;
};

export const ImagePreview = ({ id, height }: ImagePreviewProps) => {
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

    return <Image src={imageObjectURL} height={height} width={'100%'} />;
}

export const FrontAnnounceSearch = () => {
    const navigate = useNavigate();
    const [pagination, setPagination] = useState<{ current: number; pageSize: number }>({
        current: 1,
        pageSize: 6,
    });

    const [filters, setFilters] = useDebouncedState<{ field: string; operator: string; value: string }[]>([], 500);

    const { data, overtime } = useList({
        resource: "announces",
        pagination: pagination,
        filters: filters,
        overtimeOptions: {
            enabled: true,
            interval: 100,
        },
    });

    useEffect(() => {
        // Reset pagination if total items is less than current page * page size
        if (data?.meta.totalItems && data.meta.totalItems < (pagination.current * pagination.pageSize - 1)) {
            setPagination({ ...pagination, current: 1 });
        }
    }, [data]);

    const photoIds: BaseKey[] = data?.data.map((announce) => announce.photoIds).flat() || [];
    const { data: photos, isFetching: isPhotosFetching } = useMany({
        resource: "resources",
        ids: photoIds,
        queryOptions: {
            enabled: !!photoIds.length,
        },
    });

    const { data: categories } = useList({
        resource: "announces/categories",
        filters: [],
        pagination: {
            pageSize: 100,
        },
    });
    const apiUrl = useApiUrl();

    return <div style={{ position: 'relative', padding: '20px' }}>
        <LoadingOverlay visible={(overtime.elapsedTime || 0) > 100} overlayBlur={2} />
        <Title order={1} align="center" mb="20px">Announces</Title>
        <Divider size="md" my={10} />

        <TextInput
            label="Search"
            placeholder="Search..."
            onChange={(event) => setFilters([{ field: 'title', operator: 'ctn', value: event.currentTarget.value }])}
            mb="20px"
        />

        <Flex>
            <Box style={{ width: '250px', marginRight: '35px' }}>
                <Title order={4} mb="10px">Filters</Title>
                <Select
                    label="Category"
                    placeholder="Category"
                    data={categories?.data.map((category) => ({ value: category.id, label: category.name })) || []}
                    onChange={(value) => setFilters([{ field: 'categoryId', operator: 'eq', value: value?.toString() || '' }])}
                    mb="10px"
                />
                <RangeSlider
                    min={0}
                    max={2000}
                    onChangeEnd={(value) => setFilters([{ field: 'price', operator: 'btw', value: value.join(',') }])}
                    mb="10px"
                    label={(value) => new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value)}
                    step={10}
                    defaultValue={[0, 2000]}
                    marks={[
                        { value: 0, label: new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(0) },
                        { value: 2000, label: new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(2000) },
                    ]}
                />
            </Box>

            <Paper style={{ flex: 1 }} px={5} radius="md">
                <Grid my={10}>
                    {data?.data.length === 0 && (
                        <Grid.Col span={12}>
                            <Center>
                                <Stack>
                                    <Text align="center">No announces found</Text>
                                </Stack>
                            </Center>
                        </Grid.Col>
                    )}
                    {data?.data.map((announce) => (
                        <Grid.Col span={4} key={announce.id}>
                            <Card shadow="sm" p="lg" radius="md" withBorder>
                                <Card.Section>
                                    {announce.photoIds.length === 0 && (
                                        <Image src="https://placehold.co/320" height={200} alt="Placeholder" />
                                    )}
                                    {isPhotosFetching && (
                                        <Loader size="xl" />
                                    )}
                                    {!isPhotosFetching && announce.photoIds.length >= 1 && (
                                        <Carousel mx="auto" withIndicators height={200} withControls={announce.photoIds.length > 1} loop>
                                            {announce.photoIds.map((photoId, k) => {
                                                const photo = photos?.data.find((photo) => Number(photo.id) === Number(photoId));

                                                if (!photo) {
                                                    return <Carousel.Slide key={k}>
                                                        <Text color="red">Image not found</Text>
                                                    </Carousel.Slide>
                                                }

                                                return (
                                                    <Carousel.Slide key={k}>
                                                        <ImagePreview id={photoId} height={200} />
                                                    </Carousel.Slide>
                                                );
                                            })}
                                        </Carousel>
                                    )}
                                </Card.Section>

                                <Group position="apart" mt="md" mb="xs">
                                    <Text weight={500}>{announce.title}</Text>
                                    <Badge color="pink" variant="light">
                                        On Sale
                                    </Badge>
                                </Group>

                                <Text size="sm" color="dimmed" mb="xs">
                                    {announce.description}
                                </Text>
                                <Text size="sm" color="dimmed" mb="xs">
                                    {announce.price} €
                                </Text>
                                <Text size="sm" color="dimmed" mb="xs">
                                    {categories?.data.find((category) => category.id === announce.categoryId)?.name || "Category not found"}
                                </Text>

                                <Button
                                    variant="light"
                                    color="blue"
                                    fullWidth
                                    mt="md"
                                    radius="md"
                                    onClick={() => navigate(`/announces/${announce.id}`)}
                                >
                                    View
                                </Button>
                            </Card>
                        </Grid.Col>
                    ))}
                </Grid>
                <Center mt="20px">
                    <Pagination
                        total={Math.ceil((data?.meta.totalItems || 0) / pagination.pageSize)}
                        page={pagination.current}
                        onChange={(page) => setPagination({ ...pagination, current: page })}
                    />
                </Center>
            </Paper>
        </Flex>
    </div>;
};
