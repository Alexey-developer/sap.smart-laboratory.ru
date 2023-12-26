import React, { useEffect } from 'react'

import { SetPageTitle } from '@utils/SetPageTitle'

import { Container, Spinner, Alert } from 'react-bootstrap'

import constants from '@utils/constants.json'

import axios from 'axios'

import { useReactive } from 'ahooks'

export const HomePage: React.FC = () => {
  SetPageTitle('Главная')

  const state = useReactive({
    neuralNetworks: [],
    requestIsMade: false,
  })

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
        state.requestIsMade = true
      }
    } catch (error) {
      console.log(error)
    }
  }

  useEffect(() => {
    getNeuralNetworks()
  }, [])

  return (
    <Container>
      {!state.neuralNetworks.length && (
        <Alert className='mt-5' variant={'primary'}>
          <h5>
            {!state.requestIsMade ? (
              <Spinner
                className='me-2'
                as='span'
                animation='grow'
                size='sm'
                role='status'
                aria-hidden='true'
              />
            ) : (
              <i className='fa-solid fa-empty-set me-2'></i>
            )}
            {!state.requestIsMade
              ? 'Получение информации...'
              : 'У Вас ещё нет нейронных сетей'}
          </h5>
        </Alert>
      )}
      {state.neuralNetworks.length > 0 && (
        <Alert className='mt-5' variant={'primary'}>
          Количество Ваших нейросетей: {state.neuralNetworks.length} шт.
        </Alert>
      )}
    </Container>
  )
}
