import { useState, useEffect } from 'react'
import Link from 'next/link'
import Typography from '@mui/material/Typography'
import Button from '@mui/material/Button'
import classnames from 'classnames'
import type { Mode } from '@core/types'
import styles from './styles.module.css'
import frontCommonStyles from '@views/Styles/styles.module.css'

const HeroSection = ({ mode }: { mode: Mode }) => {
  const [dashboardPosition, setDashboardPosition] = useState({ x: 0, y: 0 })
  const [elementsPosition, setElementsPosition] = useState({ x: 0, y: 0 })

  // 定义图片路径
  const dashboardImageLight = '/images/front-pages/landing-page/hero-dashboard-light.png'
  const dashboardImageDark = '/images/front-pages/landing-page/hero-dashboard-dark.png'
  const elementsImageLight = '/images/front-pages/landing-page/hero-elements-light.png'
  const elementsImageDark = '/images/front-pages/landing-page/hero-elements-dark.png'
  const heroSectionBgLight = '/images/front-pages/landing-page/hero-bg-light.png'
  const heroSectionBgDark = '/images/front-pages/landing-page/hero-bg-dark.png'

  // 使用 useState 来存储实际的图片路径
  const [dashboardImage, setDashboardImage] = useState(dashboardImageLight)
  const [elementsImage, setElementsImage] = useState(elementsImageLight)
  const [heroSectionBg, setHeroSectionBg] = useState(heroSectionBgLight)

  // 根据 mode 动态设置图片路径
  useEffect(() => {
    if (mode === 'dark') {
      setDashboardImage(dashboardImageDark)
      setElementsImage(elementsImageDark)
      setHeroSectionBg(heroSectionBgDark)
    } else {
      setDashboardImage(dashboardImageLight)
      setElementsImage(elementsImageLight)
      setHeroSectionBg(heroSectionBgLight)
    }
  }, [mode])

  // 监听鼠标移动来动态调整元素位置
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const speedDashboard = 2
      const speedElements = 2.5

      const updateMousePosition = (ev: MouseEvent) => {
        const x = ev.clientX
        const y = ev.clientY

        setDashboardPosition({
          x: (window.innerWidth - x * speedDashboard) / 100,
          y: Math.max((window.innerHeight - y * speedDashboard) / 100, -40)
        })

        setElementsPosition({
          x: (window.innerWidth - x * speedElements) / 100,
          y: Math.max((window.innerHeight - y * speedElements) / 100, -40)
        })
      }

      window.addEventListener('mousemove', updateMousePosition)

      return () => {
        window.removeEventListener('mousemove', updateMousePosition)
      }
    }
  }, [])

  return (
    <section id='home' className='relative overflow-hidden pbs-[70px] -mbs-[70px] bg-backgroundPaper z-[1]'>
      <img src={heroSectionBg} alt='hero-bg' className={styles.heroSectionBg} />
      <div className={classnames('pbs-16 overflow-hidden', frontCommonStyles.layoutSpacing)}>
        <div className='md:max-is-[550px] mlb-0 mli-auto text-center'>
          <Typography className='font-extrabold text-primary sm:text-[38px] text-3xl mbe-4 leading-[44px]'>
            Smart School AI
          </Typography>
          <Typography className='font-medium' color='text.primary'>
            Smart School AI
          </Typography>
          <div className='mbs-8'>
            <Button
              component={Link}
              href='https://school.dandian.net'
              target="_blank"
              variant='contained'
              color='primary'
              size='large'
            >
              Get Early Access
            </Button>
          </div>
        </div>
      </div>
      <div
        className={classnames('relative text-center', frontCommonStyles.layoutSpacing)}
        style={{ transform: `translate(${dashboardPosition.x}px, ${dashboardPosition.y}px)`, marginTop: '40px' }}
      >
        <Link href='/' target='_blank'>
          <img src={dashboardImage} alt='dashboard-image' className={classnames('mli-auto', styles.heroSecDashboard)} />
          <div className={classnames('absolute', styles.heroSectionElements)}>
            <img
              src={elementsImage}
              alt='dashboard-elements'
              style={{ transform: `translate(${elementsPosition.x}px, ${elementsPosition.y}px)` }}
            />
          </div>
        </Link>
      </div>
    </section>
  )
}

export default HeroSection
