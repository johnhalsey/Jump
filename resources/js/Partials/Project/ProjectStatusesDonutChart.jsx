import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import {Doughnut} from "react-chartjs-2"

ChartJS.register(ArcElement, Tooltip, Legend);

export default function ProjectStatusesDonutChart ({project}) {

    let data = {
        labels: [],
        datasets: [
            {
                label: 'Project Statuses',
                data: [],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                ],
                borderWidth: 1,
            },
        ],
    };

    project.statuses.map((status, index) => {
        data.labels.push(status.name);

        data.datasets[0].data.push(status.tasks_count);
    })

    return (
        <>
            {data.labels.length && <Doughnut data={data}></Doughnut>}
        </>
    );
}
