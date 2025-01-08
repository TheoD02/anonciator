import {MarkdownField, Show, TextField} from "@refinedev/antd";
import {useOne, useShow} from "@refinedev/core";
import {Typography} from "antd";

const {Title} = Typography;

export const AnnounceShow = () => {
  const {queryResult} = useShow({});
  const {data, isLoading} = queryResult;

  const record = data?.data;

  const {data: categoryData, isLoading: categoryIsLoading} = useOne({
    resource: "announces/categories",
    id: record?.categoryId || "",
    queryOptions: {
      enabled: !!record,
    },
  });

  return (
    <Show isLoading={isLoading}>
      <Title level={5}>{"ID"}</Title>
      <TextField value={record?.id}/>
      <Title level={5}>{"Title"}</Title>
      <TextField value={record?.title}/>
      <Title level={5}>{"Content"}</Title>
      <MarkdownField value={record?.description}/>
      <Title level={5}>{"Category"}</Title>
      <TextField
        value={
          categoryIsLoading ? <>Loading...</> : <>{categoryData?.data?.name}</>
        }
      />
      <Title level={5}>{"Status"}</Title>
      <TextField value={record?.status}/>
    </Show>
  );
};
