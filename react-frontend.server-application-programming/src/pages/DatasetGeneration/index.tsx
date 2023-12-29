//@ts-nocheck

import React, { useEffect, useState, useRef } from 'react'

import { SetPageTitle } from '@utils/SetPageTitle'

import axios from 'axios'

import {
  Container,
  Row,
  Col,
  Form,
  InputGroup,
  Badge,
  Button,
  Card,
  Spinner,
  Alert,
  OverlayTrigger,
  Tooltip,
} from 'react-bootstrap'

import { useReactive } from 'ahooks'

import constants from '@utils/constants.json'

export const DatasetGeneration: React.FC = () => {
  SetPageTitle('Генерация датасета')

  const picturePortion = 30

  const [picturesQuantity, setPicturesQuantity] = useState(picturePortion)

  const state = useReactive({
    pictures: [],
    neuralNetworks: [],
    entities: [],
    activeEntityId: 0,
    picturesFound: 0,
    searching: false,
  })

  const searchInputRef = useRef<HTMLInputElement>(null)

  const searchPictures = async () => {
    const requestsQuantity = picturesQuantity / picturePortion

    state.pictures = []
    state.picturesFound = 0
    state.searching = true

    for (let i = 1; i <= requestsQuantity; i++) {
      try {
        const { data } = await axios.post(
          constants.API_URL + 'search-pictures',
          {
            search_text: searchInputRef.current?.value,
            page: i,
          },
          {
            headers: {
              Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
            },
          }
        )

        if (data.error) {
          console.log(data.error)
        } else {
          state.pictures = { ...state.pictures, ...data }
          state.picturesFound = state.picturesFound + picturePortion
          console.log(state.pictures)
        }
      } catch (error) {
        console.log(error)
      }
    }

    state.searching = false
  }

  const manageButton = entityId => {
    Number(entityId)
      ? (state.activeEntityId = Number(entityId))
      : (state.activeEntityId = 0)
  }

  const setEntities = neuralNetworkId => {
    if (Number(neuralNetworkId)) {
      state.entities = state.neuralNetworks.find(
        el => el.id == neuralNetworkId
      )?.entities
      state.activeEntityId = 0
    } else {
      state.entities = []
      state.activeEntityId = 0
    }
  }

  const getNeuralNetworks = async () => {
    try {
      const { data } = await axios.post(
        constants.API_URL + 'get-user-neural-networks',
        {},
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )

      if (data.error) {
        console.log(data.error)
      } else {
        state.neuralNetworks = data
        // state.requestIsMade = true
      }
    } catch (error) {
      console.log(error)
    }
  }

  const addImagesToEntity = async () => {
    if (state.activeEntityId === 0) return
    try {
      const { data } = await axios.post(
        constants.API_URL + 'add-images2entity',
        { images: state.pictures, entity_id: state.activeEntityId },
        {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('auth_key'),
          },
        }
      )
      if (data.error) {
        console.log(data.error)
        state.activeEntityId = 0
      } else {
        state.activeEntityId = 0
      }
    } catch (error) {
      console.log(error)
    }
  }

  useEffect(() => {
    getNeuralNetworks()
  }, [])

  return (
    <>
      <Container>
        <Row>
          <Col>
            <Form.Group className='mt-3' controlId='formBasicEmail'>
              <Form.Label>Поиск картинок</Form.Label>
              <InputGroup className='mb-3'>
                <InputGroup.Text>
                  <i className='fa-solid fa-magnifying-glass'></i>
                </InputGroup.Text>
                <Form.Control
                  ref={searchInputRef}
                  type='text'
                  placeholder='Введите поисковый запрос'
                  disabled={state.searching}
                />
              </InputGroup>
              <Form.Label>
                Количество картинок:
                <Badge className='ms-2' bg='dark'>
                  {picturesQuantity}
                </Badge>
              </Form.Label>
              <Form.Range
                min={1}
                max={picturePortion}
                defaultValue={picturesQuantity / picturePortion}
                onChange={e => {
                  setPicturesQuantity(Number(e.target.value) * picturePortion)
                }}
                disabled={state.searching}
              />
            </Form.Group>

            {!state.searching ? (
              <Button
                className='mb-2'
                variant='success'
                onClick={searchPictures}
              >
                <i className='fa-solid fa-magnifying-glass'></i> Найти
              </Button>
            ) : (
              <Button className='mb-2' variant='danger' disabled>
                <Spinner
                  as='span'
                  animation='grow'
                  size='sm'
                  role='status'
                  aria-hidden='true'
                />{' '}
                Поиск...
              </Button>
            )}
          </Col>
        </Row>
        <Row>
          <Col>
            Найдено картинок:
            <Badge className='ms-2' bg='dark'>
              {state.picturesFound}
            </Badge>
          </Col>
        </Row>
        <Row>
          <Col>
            <Form.Label className='mt-3'>
              Добавьте датасет к классу нейросети
            </Form.Label>
            <Form.Select
              onChange={e => {
                setEntities(e.target.value)
              }}
            >
              <option>Нейросеть не выбрана</option>
              {Object.keys(state.neuralNetworks).map(index => (
                <option
                  key={state.neuralNetworks[index].id}
                  value={state.neuralNetworks[index].id}
                >
                  #
                  {state.neuralNetworks[index].id +
                    ' ' +
                    state.neuralNetworks[index].name}
                </option>
              ))}
            </Form.Select>
            <Form.Select
              className='my-2'
              onChange={e => {
                manageButton(e.target.value)
              }}
            >
              <option>Класс не выбран</option>
              {Object.keys(state.entities).map(index => (
                <option
                  key={state.entities[index].id}
                  value={state.entities[index].id}
                >
                  #{state.entities[index].id + ' ' + state.entities[index].name}
                </option>
              ))}
            </Form.Select>
            {state.activeEntityId !== 0 &&
              !state.searching &&
              state.picturesFound > 0 && (
                <Button
                  className='mb-2'
                  variant='success'
                  onClick={addImagesToEntity}
                >
                  <i className='fa-solid fa-plus'></i> Добавить изображения к
                  классу
                </Button>
              )}
          </Col>
        </Row>
        <Row>
          <Col>
            <p>*Картинки находятся порциями, кратными 30 шт.</p>
            <p>
              *По кнопке под каждой картинкой Вы можете перейти на страницу, где
              была обнаружена картинка
            </p>
          </Col>
        </Row>
        <Row>
          {Object.keys(state.pictures).map((key, index) => (
            <Col key={key} xs={6} md={4}>
              <Card className='mb-2'>
                <Card.Img variant='top' src={state.pictures[key].image} />
                {/* origUrl */}
                <Card.Body>
                  <Card.Title>
                    <Badge className='me-2' bg='info'>
                      ({index + 1})
                    </Badge>
                    {state.pictures[key].snippet.title}
                  </Card.Title>
                  <Button
                    href={state.pictures[key].snippet.url}
                    target='_blank'
                    variant='primary'
                  >
                    Перейти
                  </Button>
                  <OverlayTrigger
                    placement='top'
                    overlay={<Tooltip>Удалить из выборки</Tooltip>}
                  >
                    <Button
                      className='float-end'
                      variant='danger'
                      onClick={() => {
                        delete state.pictures[key]
                        state.picturesFound--
                      }}
                    >
                      <i className='fa-solid fa-trash-xmark'></i>
                    </Button>
                  </OverlayTrigger>
                </Card.Body>
              </Card>
            </Col>
          ))}
          {!state.picturesFound && (
            <Alert variant={'primary'}>
              <h3>
                <i className='fa-solid fa-empty-set'></i> Результаты поиска
                отсутствуют
              </h3>
            </Alert>
          )}
        </Row>
      </Container>
    </>
  )
}

// : {
// 					id: string
// 					data: { origUrl: string; snippet: { title: string; url: string } }
// 				}[]
