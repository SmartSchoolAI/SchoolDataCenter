'use client'

// Component Imports
import TableData from './TableData'

// MUI Imports
import Typography from '@mui/material/Typography'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Grid from '@mui/material/Grid'

// Third-party Imports
import classnames from 'classnames'

// Styles Imports
import frontCommonStyles from '@views/Styles/styles.module.css'

interface NewsListProps {
    type: string; // Define the type of the prop
}

const NewsList = ({ type }: NewsListProps) => {

  console.log("type", type)
  
    return (
        <section className={classnames('flex flex-col gap-6 md:plb-[20px]', frontCommonStyles.layoutSpacing)}>
            <Grid container spacing={6}>
                <Grid item xs={12} lg={6}>
                    <Card sx={{my: 0}}>
                        <CardContent className='flex flex-col items-start text-center' sx={{my: 0}} >
                            <Typography variant='h5' sx={{mb: 2}}>{'学校领导'}</Typography>
                            <TableData type={'学校领导'} />
                        </CardContent>
                    </Card>
                </Grid>
                <Grid item xs={12} lg={6}>
                    <Card sx={{my: 0}}>
                        <CardContent className='flex flex-col items-start text-center' sx={{my: 0}} >
                            <Typography variant='h5' sx={{mb: 2}}>{'学校领导'}</Typography>
                            <TableData type={'学校领导'} />
                        </CardContent>
                    </Card>
                </Grid>
                <Grid item xs={12} lg={6}>
                    <Card sx={{my: 0}}>
                        <CardContent className='flex flex-col items-start text-center' sx={{my: 0}} >
                            <Typography variant='h5' sx={{mb: 2}}>{'学校领导'}</Typography>
                            <TableData type={'学校领导'} />
                        </CardContent>
                    </Card>
                </Grid>
                <Grid item xs={12} lg={6}>
                    <Card sx={{my: 0}}>
                        <CardContent className='flex flex-col items-start text-center' sx={{my: 0}} >
                            <Typography variant='h5' sx={{mb: 2}}>{'学校领导'}</Typography>
                            <TableData type={'学校领导'} />
                        </CardContent>
                    </Card>
                </Grid>
            </Grid>
        </section>
    )
}

export default NewsList
