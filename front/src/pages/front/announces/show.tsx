import { Carousel } from "@mantine/carousel";
import { Card, Image, Loader, Text, Title, Button, Box, Group, Divider, LoadingOverlay } from "@mantine/core";
import { BaseKey, useApiUrl, useMany, useOne } from "@refinedev/core"
import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router";

export const FrontAnnounceShow = () => {
    const navigate = useNavigate();
    const { id } = useParams();
    const { data, isLoading: isAnnounceLoading } = useOne({
        resource: "announces",
        id: id,
    });

    const announce = data?.data;

    const apiUrl = useApiUrl();
    const photoIds: BaseKey[] = announce?.photoIds || [];
    const { data: photos, isLoading: isPhotosLoading } = useMany({
        resource: "resources",
        ids: photoIds,
        queryOptions: {
            enabled: !!photoIds.length,
        },
    });

    const { data: category, isLoading: isCategoryLoading } = useOne({
        resource: "announces/categories",
        id: announce?.categoryId,
        queryOptions: {
            enabled: !!announce?.categoryId,
        },
    });

    const [queryConversationEnabled, setQueryConversationEnabled] = useState(false);
    const { data: conversation, isError: hasInitiatedConversationError, error: initiateConversationError } = useOne({
        resource: "conversations/initiate",
        id: announce?.id,
        queryOptions: {
            enabled: queryConversationEnabled,
        },
    });
    console.log(conversation, hasInitiatedConversationError, initiateConversationError);

    useEffect(() => {
        if (hasInitiatedConversationError) {
            console.error(initiateConversationError);
        }
    }, [hasInitiatedConversationError]);

    useEffect(() => {
        if (conversation?.data.id) {
            navigate(`/conversations/${conversation?.data.id}/messages`);
        }
    }, [conversation?.data]);

    return <div style={{ position: 'relative' }}>
        <LoadingOverlay visible={isAnnounceLoading} overlayBlur={2} />
        <div style={{ display: 'flex', justifyContent: 'space-between', padding: '20px' }}>
            <div style={{ flex: 1, marginRight: '20px' }}>
                <Title order={2} mb="md">
                    {announce?.title}
                </Title>
                <Card shadow="sm" p="lg">
                    <Card.Section>
                        {announce?.photoIds.length === 0 && (
                            <Image src="https://placehold.co/320" height={300} alt="Placeholder" />
                        )}
                        {isPhotosLoading && announce?.photoIds.length >= 1 && (
                            <Loader size="xl" />
                        )}
                        {!isPhotosLoading && announce?.photoIds.length >= 1 && (
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
                            {isCategoryLoading && <Loader size="xs" />}
                            {category?.data.name}
                        </Text>
                    </Group>
                </Card>
            </div>
            <Box style={{ width: '300px' }}>
                <div style={{ position: 'relative' }}>
                    <LoadingOverlay visible={isAnnounceLoading} overlayBlur={2} />
                    <Card shadow="sm" p="lg">
                        <Title order={4} mb="md">Posted by {announce?.createdBy}</Title>
                        <Divider my="sm" />
                        <Button
                            fullWidth
                            mt="md"
                            onClick={() => {
                                setQueryConversationEnabled(true);
                            }}
                        >
                            Send message
                        </Button>
                    </Card>
                </div>
            </Box>
        </div>
    </div>
}