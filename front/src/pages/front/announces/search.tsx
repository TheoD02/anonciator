import { Badge, Button, Card, Center, Divider, Flex, Grid, Group, Image, Loader, LoadingOverlay, Pagination, Select, Text, TextInput, Title, Box, RangeSlider } from "@mantine/core";
import { BaseKey, useApiUrl, useList, useMany } from "@refinedev/core";
import { Carousel } from '@mantine/carousel';
import { useEffect, useState } from "react";
import { useNavigate } from "react-router";
import { useDebouncedValue } from '@mantine/hooks';

export const FrontAnnounceSearch = () => {
    const navigate = useNavigate();
    const [pagination, setPagination] = useState<{ current: number; pageSize: number }>({
        current: 18,
        pageSize: 6,
    });

    const [filters, setFilters] = useState<{ field: string; operator: string; value: string }[]>([]);

    const { data, isFetching, overtime } = useList({
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
        if (data?.meta.totalItems && data.meta.totalItems < pagination.current * pagination.pageSize) {
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

    const categoryIds: BaseKey[] = data?.data.map((announce) => announce.categoryId) || [];
    const { data: categories, isFetching: isCategoriesFetching } = useMany({
        resource: "announces/categories",
        ids: categoryIds,
        queryOptions: {
            enabled: !!categoryIds.length,
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
            value={filters.find((filter) => filter.field === 'search')?.value || ''}
            onChange={(event) => setFilters([{ field: 'search', operator: 'like', value: event.currentTarget.value }])}
            mb="20px"
        />

        <Flex>
            <Box style={{ width: '250px', marginRight: '20px' }}>
                <Title order={4} mb="10px">Filters</Title>
                <Select
                    label="Category"
                    placeholder="Category"
                    data={categories?.data.map((category) => ({ value: category.id, label: category.name })) || []}
                    value={filters.find((filter) => filter.field === 'categoryId')?.value || ''}
                    onChange={(value) => setFilters([{ field: 'categoryId', operator: 'eq', value: value?.toString() || '' }])}
                    mb="10px"
                />
                <RangeSlider
                    min={0}
                    max={1000}
                    value={filters.find((filter) => filter.field === 'price')?.value || [0, 1000]}
                    onChange={(value) => setFilters([{ field: 'price', operator: 'between', value: value.join(',') }])}
                    mb="10px"
                />
            </Box>

            <div style={{ flex: 1 }}>
                <Grid my={10}>
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
                                                        <Image src={`${apiUrl}/resources/${photoId}`} height={200} alt="Announce" />
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
            </div>
        </Flex>
    </div>;
};
