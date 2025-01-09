import { useApiUrl, useList, useMany } from "@refinedev/core";
import { Card, Carousel, Col, Row } from "antd";

export const FrontAnnounceList = () => {
  const { data: announces } = useList({
    resource: "announces",
  });

  const apiUrl = useApiUrl();
  const photoIds = announces?.data?.map((announce) => announce.photoIds).flat() || [];
  const { data: photos } = useMany({
    resource: "resources",
    ids: photoIds,
    queryOptions: {
      enabled: photoIds && photoIds.length > 0,
    },
  });

  return (
    <div>
      <Row gutter={16}>
        {announces?.data.map((announce) => (
          <Col span={8}>
            <Card
              hoverable
              key={announce.id}
              title={announce.title}
              cover={
                <Carousel infinite autoplay arrows={announce.photoIds.length > 1}>
                  {announce.photoIds.map((photoId) => {
                    const photo = photos?.data?.find((photo) => Number(photo.id) === photoId);
                    console.log(photo);
                    return (
                      <img
                        key={photo?.id}
                        alt={photo?.name}
                        src={`${apiUrl}/resources/${photo?.id}`}
                        style={{ objectFit: "cover", width: "100%" }}
                      />
                    );
                  }
                  )}
                </Carousel>
              }
              bordered
            >
              <p>{announce.description}</p>
              <p>{announce.price}</p>
              <p>{announce.categoryId}</p>
              <p>{announce.location}</p>
              <p>{announce.status}</p>
            </Card>
          </Col>
        ))}
      </Row>
    </div>
  );
};

export default FrontAnnounceList;
