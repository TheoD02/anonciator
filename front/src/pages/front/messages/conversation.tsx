import { Avatar, Box, Button, Center, Flex, Group, Loader, LoadingOverlay, Paper, ScrollArea, Stack, Text, TextInput } from "@mantine/core"
import { useCreate, useInfiniteList, useOne } from "@refinedev/core";
import { IconDotsVertical, IconMessage, IconPhone, IconSearch, IconSend, IconVideo } from "@tabler/icons-react";
import { useEffect, useRef, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { TOKEN_KEY } from "../../../authProvider";

interface Message {
  id: number;
  text: string;
  sender: string;
  timestamp: Date;
}

const Sidebar = ({ conversations, isLoading, id, navigate, email }) => (
  <Box sx={{ width: 300, height: '100%', borderRight: '1px solid #eee' }}>
    <Paper shadow="xs" p="md">
      <TextInput
        placeholder="Search conversations..."
        icon={<IconSearch size={16} />}
        mb="md"
      />
      <Stack spacing="sm">
        {isLoading && <Center><Loader /></Center>}
        {conversations?.length === 0 && <Text align="center">No conversations</Text>}
        {conversations?.map((conversation, index) => (
          <Paper
            key={index}
            p="sm"
            sx={{ cursor: 'pointer', '&:hover': { backgroundColor: '#f8f9fa' }, backgroundColor: conversation.id?.toString() === id ? '#f8f9fa' : 'inherit' }}
            onClick={() => {
              navigate(`/conversations/${conversation.id}/messages`);
            }}
          >
            <Group>
              <Avatar color="blue" radius="xl">
                <IconMessage size={20} />
              </Avatar>
              <Box>
                <Text weight={500}>{conversation.name}</Text>
                <Text size="sm" color="dimmed">
                  {conversation.initializedBy === email ? 'You' : conversation.initializedBy}
                </Text>
              </Box>
            </Group>
          </Paper>
        ))}
      </Stack>
    </Paper>
  </Box>
);

const ChatHeader = ({ conversation }) => (
  <Paper p="md" radius={0} sx={{ borderBottom: '1px solid #eee' }}>
    <Group position="apart">
      <Group>
        <Avatar color="blue" radius="xl">
          <IconMessage size={20} />
        </Avatar>
        <Box>
          <Text weight={500}>
            {conversation?.name} - {conversation?.receivedBy}
          </Text>
        </Box>
      </Group>
      <Group>
        <Button variant="subtle" size="sm" px={8}>
          <IconDotsVertical size={20} />
        </Button>
      </Group>
    </Group>
  </Paper>
);

const Messages = ({ messages, isLoading, email, viewport, onScrollPositionChange }) => (
  <ScrollArea sx={{ flex: 1 }} p="md" viewportRef={viewport} onScrollPositionChange={onScrollPositionChange}>
    {isLoading && <Center><Loader /></Center>}
    <Stack spacing="md">
      {messages?.map((message) => (
        <Box
          key={message.id}
          sx={{
            display: 'flex',
            justifyContent: message.sentBy === email ? 'flex-end' : 'flex-start'
          }}
        >
          <Paper
            p="sm"
            radius="md"
            sx={(theme) => ({
              backgroundColor: message.sentBy === email ? theme.colors.blue[6] : theme.colors.gray[0],
              color: message.sentBy === email ? 'white' : 'inherit',
              maxWidth: '70%'
            })}
          >
            <Text>{message.content}</Text>
            <Text size="xs" color={message.sentBy === email ? 'gray.2' : 'dimmed'} mt={4}>
              {new Date(message.sentAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
            </Text>
          </Paper>
        </Box>
      ))}
    </Stack>
  </ScrollArea>
);

const ConversationInput = ({ newMessage, setNewMessage, sendMessage, isSendingMessage, viewport }) => (
  <Paper p="md" radius={0} sx={{ borderTop: '1px solid #eee' }}>
    <form onSubmit={(e) => {
      e.preventDefault();
      if (newMessage.trim() === '') return;
      sendMessage({}, {
        onSuccess: () => {
          setNewMessage('');
          viewport?.current?.scrollTo({ top: viewport.current.scrollHeight });
        }
      });
    }}>
      <Flex align="center" justify="space-between">
        <TextInput
          placeholder="Type a message..."
          value={newMessage}
          onChange={(e) => setNewMessage(e.target.value)}
          disabled={isSendingMessage}
          sx={{ flex: 8 }}
          mr={5}
        />
        <Button
          type="submit"
          rightIcon={<IconSend size={16} />}
          sx={{ flex: 1 }}
          loading={isSendingMessage}
        >
          Send
        </Button>
      </Flex>
    </form>
  </Paper>
);

export const FrontMessageConversation = () => {
  const navigate = useNavigate();
  const { id } = useParams();
  const { data: conversationsPages, isLoading: isConversationsLoading } = useInfiniteList({
    resource: `conversations`,
    queryOptions: {
      enabled: true,
    },
  });
  const conversations = conversationsPages?.pages?.map((page) => page.data).flat();

  const { data: conversationData, isLoading: isConversationLoading } = useOne({
    resource: `conversations`,
    id: id,
    queryOptions: {
      enabled: !!conversationsPages?.pages && conversationsPages?.pages.some((page) => page.data.some((conversation) => conversation.id === id)),
    },
  });
  const conversation = conversationData?.data || conversations?.find((conversation) => Number(conversation.id) === Number(id));

  const [scrollPosition, onScrollPositionChange] = useState({ x: 0, y: 0 });
  const {
    data: messagesInfiniteList,
    isLoading: isLoadingMessages,
    hasNextPage,
    fetchNextPage,
    isFetchingNextPage: isFetchingNextMessagesPage,
  } = useInfiniteList({
    resource: `conversations/${id}/messages`,
    pagination: {
      pageSize: 10,
    },
  });
  const messages = messagesInfiniteList?.pages?.map((page) => page.data).flat().sort((a, b) => a.id - b.id);

  useEffect(() => {
    if (isLoadingMessages === false && viewport.current) {
      viewport.current.scrollTo({ top: viewport.current.scrollHeight });
    }
  }, [isLoadingMessages]);

  useEffect(() => {
    if (scrollPosition.y < 100 && hasNextPage && !isFetchingNextMessagesPage) {
      fetchNextPage();
    }
  }, [scrollPosition]);

  const [newMessage, setNewMessage] = useState('');

  const { mutate: sendMessage, isLoading: isSendingMessage } = useCreate({
    resource: `conversations/${id}/messages`,
    successNotification: false,
    values: {
      content: newMessage
    },
  });

  const token = localStorage.getItem(TOKEN_KEY);
  const decodedToken = JSON.parse(atob(token.split('.')[1]));
  const email = decodedToken.email;

  const viewport = useRef<HTMLDivElement>(null);
  return <Paper>
    <div style={{ position: 'relative' }}>
      <LoadingOverlay visible={!conversation || isLoadingMessages || isFetchingNextMessagesPage} overlayBlur={2} />
      <Group noWrap sx={{ height: '80vh' }}>
        <Sidebar conversations={conversations} isLoading={!conversation} id={id} navigate={navigate} email={email} />
        <Box sx={{ flex: 1, display: 'flex', flexDirection: 'column', height: '100%' }}>
          {conversations?.length === 0 && <Text align="center" p="md">Please start a conversation from an announce</Text>}
          {conversations?.length > 0 && (
            <>
              <ChatHeader conversation={conversation} />
              <Messages
                messages={messages}
                isLoading={(!conversation || isLoadingMessages || isFetchingNextMessagesPage)}
                email={email}
                viewport={viewport}
                onScrollPositionChange={onScrollPositionChange}
              />
              <ConversationInput
                newMessage={newMessage}
                setNewMessage={setNewMessage}
                sendMessage={sendMessage}
                isSendingMessage={isSendingMessage}
                viewport={viewport}
              />
            </>
          )}
        </Box>
      </Group>
    </div>
  </Paper>
}
