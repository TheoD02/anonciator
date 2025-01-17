import { Avatar, Box, Button, Center, Flex, Group, Loader, LoadingOverlay, Menu, Paper, ScrollArea, Stack, Text, TextInput } from "@mantine/core"
import { useCreate, useDelete, useInfiniteList, useOne } from "@refinedev/core";
import { IconDotsVertical, IconMessage, IconSearch, IconSend } from "@tabler/icons-react";
import { useEffect, useRef, useState } from "react";
import { useNavigate, useParams } from "react-router";
import { TOKEN_KEY } from "../../../authProvider";
import { getUserInfo } from "../../../utils/userUtils";

type ConversationProps = {
  id: number;
  name: string;
  receivedBy: string;
  initializedBy: string;
};

type SidebarProps = {
  conversations: ConversationProps[];
  isLoading: boolean;
  id: string;
};

const Sidebar = ({ conversations, isLoading, id }: SidebarProps) => {
  const navigate = useNavigate();
  const user = getUserInfo();

  return (
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
                <Avatar color="blue" radius="xl" size={20}>
                  <IconMessage size={10} />
                </Avatar>
                <Box>
                  <Text weight={500} size={14}>{conversation.name}</Text>
                </Box>
              </Group>
            </Paper>
          ))}
        </Stack>
      </Paper>
    </Box>
  )
}


type ChatProps = {
  conversation: ConversationProps | null;
};

const Chat = ({ conversation }: ChatProps) => {
  const user = getUserInfo();
  const navigate = useNavigate();

  const { mutate: deleteConversation } = useDelete();

  if (!conversation) return <Center mt={20}>
    <Text>
      No conversation selected
    </Text>
  </Center>;

  return (
    <Paper p="md" radius={0} sx={{ borderBottom: '1px solid #eee' }}>
      <Group position="apart">
        <Group>
          <Avatar color="blue" radius="xl">
            <IconMessage size={20} />
          </Avatar>
          <Box>
            <Text weight={500}>
              <Stack spacing={0}>
                <Text>{conversation?.name}</Text>
                <Text size="sm" color="dimmed">
                  {conversation?.receivedBy === user?.email ? conversation?.initializedBy : conversation?.receivedBy}
                </Text>
              </Stack>
            </Text>
          </Box>
        </Group>
        <Group>
          <Menu>
            <Menu.Target>
              <Button variant="subtle" size="sm" px={8}>
                <IconDotsVertical size={20} />
              </Button>
            </Menu.Target>
            <Menu.Dropdown>
              <Menu.Item color="red" onClick={() => {
                deleteConversation({
                  resource: `conversations`,
                  id: conversation?.id,
                }, {
                  onSuccess: () => {
                    navigate('/conversations');
                  }
                });
              }}>
                Delete
              </Menu.Item>
            </Menu.Dropdown>
          </Menu>
        </Group>
      </Group>
    </Paper>
  )
}

type MessageProps = {
  id: number;
  content: string;
  sentBy: string;
  sentAt: string;
};

type MessagesProps = {
  messages: MessageProps[];
  isLoading: boolean;
  viewport: any;
  onScrollPositionChange: any;
};

const Messages = ({ messages, isLoading, viewport, onScrollPositionChange }: MessagesProps) => {
  const user = getUserInfo();

  return (
    <ScrollArea sx={{ flex: 1 }} p="md" viewportRef={viewport} onScrollPositionChange={onScrollPositionChange}>
      {isLoading && <Center><Loader /></Center>}
      <Stack spacing="md">
        {messages?.map((message) => (
          <Box
            key={message.id}
            sx={{
              display: 'flex',
              justifyContent: message.sentBy === user?.email ? 'flex-end' : 'flex-start'
            }}
          >
            <Paper
              p="sm"
              radius="md"
              sx={(theme) => ({
                backgroundColor: message.sentBy === user?.email ? theme.colors.blue[6] : theme.colors.gray[0],
                color: message.sentBy === user?.email ? 'white' : 'inherit',
                maxWidth: '70%'
              })}
            >
              <Text>{message.content}</Text>
              <Text size="xs" color={message.sentBy === user?.email ? 'gray.2' : 'dimmed'} mt={4}>
                {new Date(message.sentAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
              </Text>
            </Paper>
          </Box>
        ))}
      </Stack>
    </ScrollArea>
  )
}

type ConversationInputProps = {
  newMessage: string;
  setNewMessage: (newMessage: string) => void;
  sendMessage: (values: any, options: any) => void;
  isSendingMessage: boolean;
  viewport: any;
};

const ConversationInput = ({ newMessage, setNewMessage, sendMessage, isSendingMessage, viewport }: ConversationInputProps) => (
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
  const { id } = useParams();
  const { data: conversationsPages, isLoading: isConversationsLoading } = useInfiniteList<ConversationProps>({
    resource: `conversations`,
    queryOptions: {
      enabled: true,
    },
  });
  const conversations: ConversationProps[] = conversationsPages?.pages?.map((page) => page.data).flat() || [];

  const { data: conversationData, isLoading: isConversationLoading } = useOne<ConversationProps>({
    resource: `conversations`,
    id: id,
    queryOptions: {
      enabled: !!id && !!conversationsPages?.pages && conversationsPages?.pages.some((page) => page.data.some((conversation) => Number(conversation.id) === Number(id))),
    },
  });
  const conversation: ConversationProps | null = conversationData?.data || conversations?.find((conversation) => Number(conversation.id) === Number(id)) || null;

  const [scrollPosition, onScrollPositionChange] = useState({ x: 0, y: 0 });
  const {
    data: messagesInfiniteList,
    isLoading: isLoadingMessages,
    hasNextPage,
    fetchNextPage,
    isFetchingNextPage: isFetchingNextMessagesPage,
  } = useInfiniteList<MessageProps>({
    resource: `conversations/${id}/messages`,
    pagination: {
      pageSize: 10,
    },
    queryOptions: {
      enabled: !!id,
    },
  });
  const messages: MessageProps[] = messagesInfiniteList?.pages?.map((page) => page.data).flat().sort((a, b) => a.id - b.id) || [];

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

  const { mutate: sendMessage, isLoading: isSendingMessage } = useCreate<{ content: string }>({
    resource: `conversations/${id}/messages`,
    successNotification: false,
    values: {
      content: newMessage
    },
  });

  const viewport = useRef<HTMLDivElement>(null);
  return <Paper>
    <div style={{ position: 'relative' }}>
      <LoadingOverlay visible={!conversations} overlayBlur={2} />
      <Group noWrap sx={{ height: '80vh' }}>
        <Sidebar conversations={conversations} isLoading={!conversations} id={id} />
        <Box sx={{ flex: 1, display: 'flex', flexDirection: 'column', height: '100%' }}>
          {conversations?.length === 0 && <Text align="center" p="md">Please start a conversation from an announce</Text>}
          {(conversations?.length || 0) > 0 && (
            <>
              <Chat conversation={conversation} />
              <Messages
                messages={messages}
                isLoading={id !== undefined && (!conversation || isLoadingMessages || isFetchingNextMessagesPage)}
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
