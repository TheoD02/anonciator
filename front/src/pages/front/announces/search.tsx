import { Badge, Button, Card, Center, Divider, Flex, Grid, Group, Image, Loader, LoadingOverlay, Pagination, Text, Title } from "@mantine/core";
import { BaseKey, useApiUrl, useList, useMany } from "@refinedev/core";
import { Carousel } from '@mantine/carousel';
import { useState } from "react";

export const AnnounceSearch = () => {
    const [pagination, setPagination] = useState<{ current: number; pageSize: number }>({
        current: 18,
        pageSize: 6,
    });

    const { data, isFetching, overtime } = useList({
        resource: "announces",
        pagination: pagination,
        overtimeOptions: {
            enabled: true,
            interval: 100,
        },
    });

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

    return <div style={{ position: 'relative' }}>
        <LoadingOverlay visible={(overtime.elapsedTime || 0) > 100} overlayBlur={2} />
            <Title order={1} align="center">Announces</Title>
        <Divider size="md" my={10} />
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

                        <Text size="sm" color="dimmed">
                            {announce.description}
                        </Text>
                        <Text size="sm" color="dimmed">
                            {announce.price} €
                        </Text>
                        <Text size="sm" color="dimmed">
                            {categories?.data.find((category) => category.id === announce.categoryId)?.name || "Category not found"}
                        </Text>

                        <Button variant="light" color="blue" fullWidth mt="md" radius="md">
                            View
                        </Button>
                    </Card>
                </Grid.Col>
            ))}
        </Grid>
        <Center>
            <Pagination
                total={((data?.meta.totalItems || 0) / pagination.pageSize) + 1}
                page={pagination.current}
                onChange={(page) => setPagination({ ...pagination, current: page })}
            />
        </Center>
    </div>;
};