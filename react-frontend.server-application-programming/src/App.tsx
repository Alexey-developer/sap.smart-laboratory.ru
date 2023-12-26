import { Routes, Route } from 'react-router-dom'

import { MainLayout } from '@layouts/MainLayout'

import { HomePage } from '@pages/HomePage'
import { AuthPage } from '@pages/AuthPage'
import { DatasetGeneration } from '@pages/DatasetGeneration'
import { NeuralNetworks } from '@pages/NeuralNetworks'
import { NeuralNetwork } from '@pages/NeuralNetwork'
import { Entity } from '@pages/Entity'

function App() {
  return (
    <Routes>
      <Route path='/' element={<MainLayout />}>
        <Route path='' element={<HomePage />} />
        <Route path='/dataset-generation' element={<DatasetGeneration />} />
        <Route path='/neural-networks' element={<NeuralNetworks />} />
        <Route path='/neural-networks/:id' element={<NeuralNetwork />} />
        <Route path='/classes/:id' element={<Entity />} />
        <Route
          path='*'
          element={
            <h1 className='text-center m-5'>Страница не найдена: 404</h1>
          }
        />
      </Route>
      <Route path='auth' element={<AuthPage />} />
    </Routes>
  )
}

export default App
