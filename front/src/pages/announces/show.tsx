import { MarkdownField, Show, TextField } from "@refinedev/antd";
import { useMany, useOne, useShow } from "@refinedev/core";
import { Carousel, Typography } from "antd";

const { Title } = Typography;

export const AnnounceShow = () => {
  const { queryResult } = useShow({});
  const { data, isLoading } = queryResult;

  const record = data?.data;

  const { data: categoryData, isLoading: categoryIsLoading } = useOne({
    resource: "announces/categories",
    id: record?.categoryId || "",
    queryOptions: {
      enabled: !!record,
    },
  });

  const { data: photosData, isLoading: photosIsLoading } = useMany({
    resource: "resources",
    ids: record?.photoIds || [],
    queryOptions: {
      enabled: !!record && record?.photoIds?.length > 0,
    },
  });

  console.log("record", record);
  const contentStyle: React.CSSProperties = {
    margin: 0,
    height: "60px",
    color: "#fff",
    lineHeight: "160px",
    textAlign: "center",
    background: "#364d79",
    display: "flex",
  };
  return (
    <Show isLoading={isLoading}>
      <Title level={5}>{"ID"}</Title>
      <TextField value={record?.id} />
      <Title level={5}>{"Title"}</Title>
      <TextField value={record?.title} />
      <Title level={5}>{"Content"}</Title>
      <MarkdownField value={record?.description} />
      <Title level={5}>{"Category"}</Title>
      <TextField
        value={
          categoryIsLoading ? <>Loading...</> : <>{categoryData?.data?.name}</>
        }
      />
      <Title level={5}>{"Status"}</Title>
      <TextField value={record?.status} />
      <Title level={5}>{"Photos"}</Title>
      <div>
        {photosIsLoading ? (
          <>Loading...</>
        ) : (
          <Carousel arrows infinite={true} autoplay>
            {photosData?.data.map((photo) => (
              <div style={contentStyle} key={photo.id}>
                <img
                  key={photo.id}
                  src={`https://anonciator.api.localhost/api/resources/${photo.id}`}
                  alt={photo.originalName}
                />
              </div>
            ))}
          </Carousel>
        )}
      </div>
    </Show>
  );
};
