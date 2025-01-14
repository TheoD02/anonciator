import { Carousel } from "@mantine/carousel";
import { Card, Image, Loader, Text, Title, Button, Box, Group, Divider } from "@mantine/core";
import { BaseKey, useApiUrl, useMany, useOne } from "@refinedev/core"
import { useNavigate } from "react-router";

export const FrontAnnounceShow = () => {
    const navigate = useNavigate();
    const { data } = useOne({
        resource: "announces",
        id: 1,
    });

    const announce = data?.data;

    const apiUrl = useApiUrl();
    const photoIds: BaseKey[] = announce?.photoIds || [];
    const { data: photos, isFetching: isPhotosFetching } = useMany({
        resource: "resources",
        ids: photoIds,
        queryOptions: {
            enabled: !!photoIds.length,
        },
    });

    const { data: category, isFetching: isCategoryFetching } = useOne({
        resource: "announces/categories",
        id: announce?.categoryId,
        queryOptions: {
            enabled: !!announce?.categoryId,
        },
    });

    const { data: user, isFetching: isUserFetching } = useOne({
        resource: "users",
        id: announce?.userId,
        queryOptions: {
            enabled: !!announce?.userId,
        },
    });

    return <div style={{ display: 'flex', justifyContent: 'space-between', padding: '20px' }}>
        <div style={{ flex: 1, marginRight: '20px' }}>
            <Title order={2} mb="md">
                {announce?.title}
            </Title>
            <Card shadow="sm" padding="lg">
                <Card.Section>
                    {announce?.photoIds.length === 0 && (
                        <Image src="https://placehold.co/320" height={300} alt="Placeholder" />
                    )}
                    {isPhotosFetching && (
                        <Loader size="xl" />
                    )}
                    {!isPhotosFetching && announce?.photoIds.length >= 1 && (
                        <Carousel mx="auto" withIndicators height={300} withControls={announce?.photoIds.length > 1} loop>
                            {announce?.photoIds.map((photoId, k) => {
                                const photo = photos?.data.find((photo) => Number(photo.id) === Number(photoId));

                                if (!photo) {
                                    return <Carousel.Slide key={k}>
                                        <Text color="red">Image not found</Text>
                                    </Carousel.Slide>
                                }

                                return (
                                    <Carousel.Slide key={k}>
                                        <Image src={`${apiUrl}/resources/${photoId}`} height={300} alt="Announce" />
                                    </Carousel.Slide>
                                );
                            })}
                        </Carousel>
                    )}
                </Card.Section>
                <Text mt="md">
                    {announce?.description}
                </Text>
                <Group mt="md" position="apart">
                    <Text weight={500} size="lg">
                        ${announce?.price}
                    </Text>
                    <Text color="dimmed">
                        {category?.data.name}
                    </Text>
                </Group>
            </Card>
        </div>
        <Box style={{ width: '300px' }}>
            <Card shadow="sm" padding="lg">
                <Title order={4} mb="md">Posted by</Title>
                {isUserFetching ? (
                    <Loader size="sm" />
                ) : (
                    <Text weight={500}>{user?.data.username}</Text>
                )}
                <Divider my="sm" />
                <Button
                    fullWidth
                    mt="md"
                    onClick={() => navigate(`/conversation`)}
                >Send message</Button>
            </Card>
        </Box>
    </div>
}