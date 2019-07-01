-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 01, 2019 at 04:09 
-- Server version: 5.6.21
-- PHP Version: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sca`
--

-- --------------------------------------------------------

--
-- Table structure for table `abastecimento`
--

CREATE TABLE IF NOT EXISTS `abastecimento` (
  `dt_abastecimento` datetime NOT NULL,
  `viatura_eb` char(10) NOT NULL,
  `dt_autorizacao` datetime NOT NULL,
  `qtd_autorizada` int(11) NOT NULL,
  `qtd_abastecida` int(11) DEFAULT NULL,
  `motorista` char(50) NOT NULL,
  `od_atual` int(11) DEFAULT NULL,
  `cod_seguranca` char(40) DEFAULT NULL,
  `missao` varchar(100) DEFAULT NULL,
  `local` varchar(50) DEFAULT NULL,
  `usuario_autz` char(12) NOT NULL,
  `usuario_idt` char(12) DEFAULT NULL,
  `reservatorio_codigo` char(2) DEFAULT NULL,
  `cod_tp_cota` int(11) NOT NULL,
  `cota_om` char(6) DEFAULT NULL COMMENT 'OM detentora do combustivel',
  `tp_abastecimento` varchar(30) NOT NULL,
  `horimetro` decimal(10,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `auditoria`
--

CREATE TABLE IF NOT EXISTS `auditoria` (
  `dt_atividade` datetime NOT NULL,
  `usuario_idt` char(12) NOT NULL,
  `atividade` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auditoria`
--

INSERT INTO `auditoria` (`dt_atividade`, `usuario_idt`, `atividade`) VALUES
('2019-06-19 15:19:59', '0309755346', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-19 15:20:24', '0309755346', 'ALTEROU O CADASTRO DO USUÁRIO ROSCHEL'),
('2019-06-19 15:20:35', '0309755346', 'ALTEROU O CADASTRO DO USUÁRIO CICERO'),
('2019-06-19 15:20:56', '0309755346', 'ALTEROU OU CADASTRO DA OM CMDO 11ª BDA INF L'),
('2019-06-19 15:29:34', '0309755346', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-19 15:33:46', '0309755346', 'ALTEROU O CADASTRO DO USUÁRIO ROSCHEL'),
('2019-06-19 15:33:59', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-19 15:34:09', '0202188678', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-25 15:44:47', '0309755346', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-25 15:58:05', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-25 16:04:06', '0309755346', 'USUÏ¿½RIO ACESSOU O SISTEMA'),
('2019-06-25 16:08:29', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-25 16:10:48', '0309755346', 'USUÏ¿½RIO ACESSOU O SISTEMA'),
('2019-06-25 16:19:12', '0309755346', 'ALTEROU OU CADASTRO DA OM CMDO 11Âª BDA INF L'),
('2019-06-25 16:19:38', '0309755346', 'ALTEROU OU CADASTRO DA OM 2Âª B LOG'),
('2019-06-26 10:17:34', '0309755346', 'ALTEROU OU CADASTRO DA OM 2Âª B LOG'),
('2019-06-26 11:24:06', '0309755346', 'USUÃ¡RIO TENTOU ALTERAR O CADASTRO DA OM 2Âª B LOG'),
('2019-06-26 11:24:19', '0309755346', 'USUÃ¡RIO TENTOU ALTERAR O CADASTRO DA OM 2Âª B LOG'),
('2019-06-26 13:46:09', '0309755346', 'USUÃ¡RIO TENTOU ALTERAR O CADASTRO DA OM 2Âª B LOG'),
('2019-06-26 14:02:13', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:02:33', '0309755346', 'USUÏ¿½RIO ACESSOU O SISTEMA'),
('2019-06-26 14:04:41', '0309755346', 'ALTEROU OU CADASTRO DA OM 2º B LOG'),
('2019-06-26 14:05:21', '0309755346', 'ALTEROU OU CADASTRO DA OM CMDO 11ª BDA INF L'),
('2019-06-26 14:06:58', '0309755346', 'USUÃ¡RIO TENTOU ALTERAR O CADASTRO DA OM 2º B LOG'),
('2019-06-26 14:07:12', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:07:31', '0309755346', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-26 14:08:20', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:08:38', '0309755346', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-26 14:08:51', '0309755346', 'TENTOU REMOVER O USUÁRIO 1º TEN ROSCHEL'),
('2019-06-26 14:09:19', '0309755346', 'ALTEROU O CADASTRO DO USUÁRIO ROSCHEL'),
('2019-06-26 14:09:24', '0309755346', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:09:30', '0202188678', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-26 14:11:38', '0202188678', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:12:25', '0202188678', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-26 14:17:06', '0202188678', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:17:19', '0202188678', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-26 14:40:07', '0202188678', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 14:40:14', '0202188678', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-26 14:48:32', '0202188678', 'ALTEROU OU CADASTRO DA OM 2Âº B LOG L'),
('2019-06-26 14:48:55', '0202188678', 'ALTEROU OU CADASTRO DA OM CMDO 11Âª BDA INF L'),
('2019-06-26 15:21:58', '0202188678', 'USUÃ¡RIO TENTOU ALTERAR O CADASTRO DA OM 2Âº B LOG L'),
('2019-06-26 15:22:23', '0202188678', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-26 15:22:28', '0202188678', 'USUÁRIO ACESSOU O SISTEMA'),
('2019-06-27 10:17:50', '0202188678', 'USUÁRIO SAIU DO SISTEMA'),
('2019-06-27 14:14:51', '0202188678', 'USUÃRIO ACESSOU O SISTEMA'),
('2019-06-27 14:15:09', '0202188678', 'ALTEROU O CADASTRO DO USUÃRIO ROSCHEL'),
('2019-06-27 14:15:20', '0202188678', 'ALTEROU O CADASTRO DO USUÃRIO CICERO'),
('2019-06-27 14:15:27', '0202188678', 'USUÃRIO SAIU DO SISTEMA'),
('2019-06-27 14:15:32', '0202188678', 'USUÃRIO ACESSOU O SISTEMA'),
('2019-06-27 14:27:03', '0202188678', 'USUÃRIO SAIU DO SISTEMA'),
('2019-06-27 14:29:15', '0202188678', 'USUÃRIO ACESSOU O SISTEMA'),
('2019-06-27 14:38:30', '0202188678', 'USUÃRIO ACESSOU O SISTEMA');

-- --------------------------------------------------------

--
-- Table structure for table `combustivel`
--

CREATE TABLE IF NOT EXISTS `combustivel` (
  `codigo` char(2) NOT NULL,
  `nome` char(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `combustivel`
--

INSERT INTO `combustivel` (`codigo`, `nome`) VALUES
('1', 'Gasolina'),
('2', 'Diesel');

-- --------------------------------------------------------

--
-- Table structure for table `credito`
--

CREATE TABLE IF NOT EXISTS `credito` (
  `cod_credito` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `combustivel_codigo` char(2) NOT NULL,
  `cod_tp_cota` int(11) NOT NULL,
  `desc_credito` varchar(100) NOT NULL,
  `qtd_destinada` int(11) NOT NULL,
  `qtd_atual` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `destinatario`
--

CREATE TABLE IF NOT EXISTS `destinatario` (
  `data_hora` datetime NOT NULL,
  `remetente_idt` char(15) NOT NULL,
  `destinatario_idt` char(15) NOT NULL,
  `leitura` datetime DEFAULT NULL,
  `arquivada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `destino_credito`
--

CREATE TABLE IF NOT EXISTS `destino_credito` (
  `cod_credito` int(11) NOT NULL,
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `dt_repasse` datetime NOT NULL,
  `qtd_repassada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `estoque`
--

CREATE TABLE IF NOT EXISTS `estoque` (
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `dt_repasse` datetime NOT NULL,
  `nr_nota_fiscal` char(15) NOT NULL,
  `combustivel_codigo` char(2) NOT NULL,
  `reservatorio_codigo` char(2) NOT NULL,
  `cod_tp_cota` int(11) NOT NULL,
  `nr_remessa` char(20) NOT NULL,
  `qtd_destinada` int(11) NOT NULL,
  `qtd_atual` decimal(11,3) NOT NULL,
  `obs` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `evaporacao`
--

CREATE TABLE IF NOT EXISTS `evaporacao` (
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `data_registro` date NOT NULL,
  `qtd_evaporada` decimal(11,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `historico_abastecimento`
--

CREATE TABLE IF NOT EXISTS `historico_abastecimento` (
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `dt_abastecimento` datetime NOT NULL,
  `viatura_eb` char(10) NOT NULL,
  `qtd_abastecida` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `historico_estoque`
--

CREATE TABLE IF NOT EXISTS `historico_estoque` (
  `data` date NOT NULL,
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `qtd_atual` decimal(11,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `historico_repasse`
--

CREATE TABLE IF NOT EXISTS `historico_repasse` (
  `dt_registro` datetime NOT NULL,
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `registro` varchar(45) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `om_destino` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mensagem`
--

CREATE TABLE IF NOT EXISTS `mensagem` (
  `data_hora` datetime NOT NULL,
  `remetente_idt` char(15) NOT NULL,
  `assunto` varchar(50) NOT NULL,
  `texto` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `om`
--

CREATE TABLE IF NOT EXISTS `om` (
  `codom` char(6) NOT NULL,
  `sigla` char(30) NOT NULL,
  `nome` char(70) NOT NULL,
  `tipo` char(15) NOT NULL COMMENT 'OC, C Mil A, Cmdo RM, Cmdo DE, Cmdo Bda, Integrante, Não Integrante',
  `oc` char(6) DEFAULT NULL,
  `subordinacao` char(6) DEFAULT NULL,
  `rm` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `om`
--

INSERT INTO `om` (`codom`, `sigla`, `nome`, `tipo`, `oc`, `subordinacao`, `rm`) VALUES
('012237', '2Âº B LOG L', '2Âº BATALHÃƒO LOGÃSTICO', 'Integrante', '024794', '024794', '2'),
('024794', 'CMDO 11Âª BDA INF L', 'COMANDO DA 11Âª BRIGADA DE INFANTARIA LEVE', 'OC', '024794', '024794', '2');

-- --------------------------------------------------------

--
-- Table structure for table `recebimento`
--

CREATE TABLE IF NOT EXISTS `recebimento` (
  `nr_nota_fiscal` char(15) NOT NULL,
  `combustivel_codigo` char(2) NOT NULL,
  `oc` char(6) NOT NULL,
  `reservatorio_codigo` char(2) NOT NULL,
  `dt_rec` date NOT NULL,
  `qtd_rec` int(11) NOT NULL,
  `nr_pedido` char(20) DEFAULT NULL,
  `nr_remessa` char(20) DEFAULT NULL,
  `contrato` varchar(20) NOT NULL,
  `valor` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reservatorio`
--

CREATE TABLE IF NOT EXISTS `reservatorio` (
  `codigo` char(2) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `combustivel_codigo` char(2) NOT NULL,
  `capacidade` int(11) NOT NULL,
  `situacao` varchar(10) NOT NULL DEFAULT 'Ativo' COMMENT 'Ativo ou Inativo',
  `evaporacao` decimal(11,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sangria`
--

CREATE TABLE IF NOT EXISTS `sangria` (
  `nr_repasse` int(11) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `data_sangria` date NOT NULL,
  `usuario_idt` char(12) NOT NULL,
  `qtd_retirada` decimal(11,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tipo_cota`
--

CREATE TABLE IF NOT EXISTS `tipo_cota` (
  `cod_tp_cota` int(11) NOT NULL,
  `tipo_cota` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tipo_cota`
--

INSERT INTO `tipo_cota` (`cod_tp_cota`, `tipo_cota`) VALUES
(0, 'Geral'),
(1, 'Administrativo'),
(2, 'Operacional'),
(3, 'Inteligência'),
(4, 'Ensino'),
(5, 'Manutenção'),
(6, 'Apoio'),
(7, 'Trânsito'),
(8, 'SFPC'),
(9, 'Outros');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `idt` char(12) NOT NULL,
  `post_grad` char(10) NOT NULL,
  `nome` char(50) NOT NULL,
  `nome_guerra` char(20) DEFAULT NULL,
  `om_codom` char(6) NOT NULL,
  `perfil` char(20) NOT NULL,
  `situacao` char(20) NOT NULL,
  `login` char(20) NOT NULL,
  `senha` char(40) DEFAULT NULL,
  `gerente` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`idt`, `post_grad`, `nome`, `nome_guerra`, `om_codom`, `perfil`, `situacao`, `login`, `senha`, `gerente`) VALUES
('0202188678', 'f1Âº TEN', 'ROSCHEL', 'ROSCHEL', '024794', 'ADMINISTRADOR *', 'PRONTO NA OM', '0202188678', 'b9wrWoFtz1QjY', 'NÃƒ'),
('0309755346', 'g2Âº TEN', 'CICERO', 'CICERO', '024794', 'ADMINISTRADOR *', 'PRONTO NA OM', '0309755346', 'e82CIIcb4cwJk', 'NÃƒ');

-- --------------------------------------------------------

--
-- Table structure for table `viatura`
--

CREATE TABLE IF NOT EXISTS `viatura` (
  `eb` char(10) NOT NULL,
  `combustivel_codigo` char(2) NOT NULL,
  `om_codom` char(6) NOT NULL,
  `marca` char(20) NOT NULL,
  `modelo` char(20) NOT NULL,
  `disponivel` char(3) NOT NULL,
  `consumo` decimal(4,2) DEFAULT NULL,
  `cap_tanque` int(11) NOT NULL,
  `situacao` char(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abastecimento`
--
ALTER TABLE `abastecimento`
 ADD PRIMARY KEY (`dt_abastecimento`,`viatura_eb`), ADD KEY `abasteceimento_FKIndex1` (`viatura_eb`), ADD KEY `fk_abastecimento_usuario1_idx` (`usuario_idt`), ADD KEY `fk_abastecimento_usuario2_idx` (`usuario_autz`), ADD KEY `fk_abastecimento_reservatorio1_idx` (`reservatorio_codigo`), ADD KEY `fk_abastecimento_tipo_cota1_idx` (`cod_tp_cota`), ADD KEY `fk_abastecimento_om1_idx` (`cota_om`);

--
-- Indexes for table `auditoria`
--
ALTER TABLE `auditoria`
 ADD PRIMARY KEY (`dt_atividade`,`usuario_idt`), ADD KEY `auditoria_ibfk_1` (`usuario_idt`);

--
-- Indexes for table `combustivel`
--
ALTER TABLE `combustivel`
 ADD PRIMARY KEY (`codigo`);

--
-- Indexes for table `credito`
--
ALTER TABLE `credito`
 ADD PRIMARY KEY (`cod_credito`,`om_codom`), ADD KEY `fk_credito_tipo_cota1_idx` (`cod_tp_cota`), ADD KEY `fk_credito_combustivel1_idx` (`combustivel_codigo`), ADD KEY `fk_credito_om1_idx` (`om_codom`);

--
-- Indexes for table `destinatario`
--
ALTER TABLE `destinatario`
 ADD PRIMARY KEY (`data_hora`,`remetente_idt`,`destinatario_idt`), ADD KEY `destinatario_FKIndex1` (`data_hora`,`remetente_idt`), ADD KEY `destinatario_ibfk_2` (`destinatario_idt`);

--
-- Indexes for table `destino_credito`
--
ALTER TABLE `destino_credito`
 ADD PRIMARY KEY (`cod_credito`,`nr_repasse`,`om_codom`), ADD KEY `fk_abastecimento_has_credito_credito1_idx` (`cod_credito`), ADD KEY `fk_historico_credito_estoque1_idx` (`nr_repasse`,`om_codom`);

--
-- Indexes for table `estoque`
--
ALTER TABLE `estoque`
 ADD PRIMARY KEY (`nr_repasse`,`om_codom`), ADD KEY `cotas_FKIndex2` (`om_codom`), ADD KEY `fk_cotas_recebimento1_idx` (`nr_nota_fiscal`,`combustivel_codigo`), ADD KEY `fk_cotas_reservatorio1_idx` (`reservatorio_codigo`), ADD KEY `fk_cotas_tipo_cota1_idx` (`cod_tp_cota`);

--
-- Indexes for table `evaporacao`
--
ALTER TABLE `evaporacao`
 ADD PRIMARY KEY (`nr_repasse`,`om_codom`,`data_registro`);

--
-- Indexes for table `historico_abastecimento`
--
ALTER TABLE `historico_abastecimento`
 ADD PRIMARY KEY (`nr_repasse`,`om_codom`,`dt_abastecimento`,`viatura_eb`), ADD KEY `fk_estoque_has_abastecimento_abastecimento1_idx` (`dt_abastecimento`,`viatura_eb`), ADD KEY `fk_estoque_has_abastecimento_estoque1_idx` (`nr_repasse`,`om_codom`);

--
-- Indexes for table `historico_estoque`
--
ALTER TABLE `historico_estoque`
 ADD PRIMARY KEY (`data`,`nr_repasse`,`om_codom`), ADD KEY `fk_historico_estoque_estoque1_idx` (`nr_repasse`,`om_codom`);

--
-- Indexes for table `historico_repasse`
--
ALTER TABLE `historico_repasse`
 ADD PRIMARY KEY (`dt_registro`,`nr_repasse`,`om_codom`), ADD KEY `fk_historico_repasse_om1_idx` (`om_destino`), ADD KEY `fk_historico_repasse_estoque1` (`nr_repasse`,`om_codom`);

--
-- Indexes for table `mensagem`
--
ALTER TABLE `mensagem`
 ADD PRIMARY KEY (`data_hora`,`remetente_idt`), ADD KEY `mensagem_ibfk_1` (`remetente_idt`);

--
-- Indexes for table `om`
--
ALTER TABLE `om`
 ADD PRIMARY KEY (`codom`), ADD KEY `om_ibfk_1` (`oc`), ADD KEY `om_ibfk_2` (`subordinacao`);

--
-- Indexes for table `recebimento`
--
ALTER TABLE `recebimento`
 ADD PRIMARY KEY (`nr_nota_fiscal`,`combustivel_codigo`), ADD KEY `recebimento_ibfk_1` (`combustivel_codigo`), ADD KEY `recebimento_ibfk_2` (`reservatorio_codigo`), ADD KEY `recebimento_ibfk_3` (`oc`);

--
-- Indexes for table `reservatorio`
--
ALTER TABLE `reservatorio`
 ADD PRIMARY KEY (`codigo`), ADD KEY `reservatorio_ibfk_1` (`om_codom`), ADD KEY `fk_reservatorio_combustivel1` (`combustivel_codigo`);

--
-- Indexes for table `sangria`
--
ALTER TABLE `sangria`
 ADD PRIMARY KEY (`nr_repasse`,`om_codom`,`data_sangria`), ADD KEY `fk_sangria_estoque1_idx` (`nr_repasse`,`om_codom`), ADD KEY `fk_sangria_usuario1_idx` (`usuario_idt`);

--
-- Indexes for table `tipo_cota`
--
ALTER TABLE `tipo_cota`
 ADD PRIMARY KEY (`cod_tp_cota`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
 ADD PRIMARY KEY (`idt`), ADD UNIQUE KEY `login` (`login`), ADD KEY `usuario_ibfk_1` (`om_codom`);

--
-- Indexes for table `viatura`
--
ALTER TABLE `viatura`
 ADD PRIMARY KEY (`eb`), ADD KEY `viatura_ibfk_1` (`om_codom`), ADD KEY `viatura_ibfk_2` (`combustivel_codigo`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abastecimento`
--
ALTER TABLE `abastecimento`
ADD CONSTRAINT `abastecimento_ibfk_2` FOREIGN KEY (`viatura_eb`) REFERENCES `viatura` (`eb`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_abastecimento_om1` FOREIGN KEY (`cota_om`) REFERENCES `om` (`codom`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_abastecimento_reservatorio1` FOREIGN KEY (`reservatorio_codigo`) REFERENCES `reservatorio` (`codigo`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_abastecimento_tipo_cota1` FOREIGN KEY (`cod_tp_cota`) REFERENCES `tipo_cota` (`cod_tp_cota`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_abastecimento_usuario1` FOREIGN KEY (`usuario_idt`) REFERENCES `usuario` (`idt`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_abastecimento_usuario2` FOREIGN KEY (`usuario_autz`) REFERENCES `usuario` (`idt`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `auditoria`
--
ALTER TABLE `auditoria`
ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_idt`) REFERENCES `usuario` (`idt`) ON UPDATE CASCADE;

--
-- Constraints for table `credito`
--
ALTER TABLE `credito`
ADD CONSTRAINT `fk_credito_combustivel1` FOREIGN KEY (`combustivel_codigo`) REFERENCES `combustivel` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_credito_om1` FOREIGN KEY (`om_codom`) REFERENCES `om` (`codom`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_credito_tipo_cota1` FOREIGN KEY (`cod_tp_cota`) REFERENCES `tipo_cota` (`cod_tp_cota`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `destinatario`
--
ALTER TABLE `destinatario`
ADD CONSTRAINT `destinatario_ibfk_1` FOREIGN KEY (`data_hora`, `remetente_idt`) REFERENCES `mensagem` (`data_hora`, `remetente_idt`) ON UPDATE CASCADE,
ADD CONSTRAINT `destinatario_ibfk_2` FOREIGN KEY (`destinatario_idt`) REFERENCES `usuario` (`idt`) ON UPDATE CASCADE;

--
-- Constraints for table `destino_credito`
--
ALTER TABLE `destino_credito`
ADD CONSTRAINT `fk_abastecimento_has_credito_credito1` FOREIGN KEY (`cod_credito`) REFERENCES `credito` (`cod_credito`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_historico_credito_estoque1` FOREIGN KEY (`nr_repasse`, `om_codom`) REFERENCES `estoque` (`nr_repasse`, `om_codom`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `estoque`
--
ALTER TABLE `estoque`
ADD CONSTRAINT `cotas_ibfk_1` FOREIGN KEY (`om_codom`) REFERENCES `om` (`codom`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_cotas_recebimento1` FOREIGN KEY (`nr_nota_fiscal`, `combustivel_codigo`) REFERENCES `recebimento` (`nr_nota_fiscal`, `combustivel_codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_cotas_reservatorio1` FOREIGN KEY (`reservatorio_codigo`) REFERENCES `reservatorio` (`codigo`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_cotas_tipo_cota1` FOREIGN KEY (`cod_tp_cota`) REFERENCES `tipo_cota` (`cod_tp_cota`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `evaporacao`
--
ALTER TABLE `evaporacao`
ADD CONSTRAINT `fk_evaporacao_estoque1` FOREIGN KEY (`nr_repasse`, `om_codom`) REFERENCES `estoque` (`nr_repasse`, `om_codom`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `historico_abastecimento`
--
ALTER TABLE `historico_abastecimento`
ADD CONSTRAINT `fk_estoque_has_abastecimento_abastecimento1` FOREIGN KEY (`dt_abastecimento`, `viatura_eb`) REFERENCES `abastecimento` (`dt_abastecimento`, `viatura_eb`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_estoque_has_abastecimento_estoque1` FOREIGN KEY (`nr_repasse`, `om_codom`) REFERENCES `estoque` (`nr_repasse`, `om_codom`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `historico_estoque`
--
ALTER TABLE `historico_estoque`
ADD CONSTRAINT `fk_historico_estoque_estoque1` FOREIGN KEY (`nr_repasse`, `om_codom`) REFERENCES `estoque` (`nr_repasse`, `om_codom`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `historico_repasse`
--
ALTER TABLE `historico_repasse`
ADD CONSTRAINT `fk_historico_repasse_estoque1` FOREIGN KEY (`nr_repasse`, `om_codom`) REFERENCES `estoque` (`nr_repasse`, `om_codom`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_historico_repasse_om1` FOREIGN KEY (`om_destino`) REFERENCES `om` (`codom`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `mensagem`
--
ALTER TABLE `mensagem`
ADD CONSTRAINT `mensagem_ibfk_1` FOREIGN KEY (`remetente_idt`) REFERENCES `usuario` (`idt`) ON UPDATE CASCADE;

--
-- Constraints for table `om`
--
ALTER TABLE `om`
ADD CONSTRAINT `om_ibfk_1` FOREIGN KEY (`oc`) REFERENCES `om` (`codom`) ON UPDATE CASCADE,
ADD CONSTRAINT `om_ibfk_2` FOREIGN KEY (`subordinacao`) REFERENCES `om` (`codom`) ON UPDATE CASCADE;

--
-- Constraints for table `recebimento`
--
ALTER TABLE `recebimento`
ADD CONSTRAINT `recebimento_ibfk_1` FOREIGN KEY (`combustivel_codigo`) REFERENCES `combustivel` (`codigo`) ON UPDATE CASCADE,
ADD CONSTRAINT `recebimento_ibfk_2` FOREIGN KEY (`reservatorio_codigo`) REFERENCES `reservatorio` (`codigo`) ON UPDATE CASCADE,
ADD CONSTRAINT `recebimento_ibfk_3` FOREIGN KEY (`oc`) REFERENCES `om` (`codom`) ON UPDATE CASCADE;

--
-- Constraints for table `reservatorio`
--
ALTER TABLE `reservatorio`
ADD CONSTRAINT `fk_reservatorio_combustivel1` FOREIGN KEY (`combustivel_codigo`) REFERENCES `combustivel` (`codigo`) ON UPDATE CASCADE,
ADD CONSTRAINT `reservatorio_ibfk_1` FOREIGN KEY (`om_codom`) REFERENCES `om` (`codom`) ON UPDATE CASCADE;

--
-- Constraints for table `sangria`
--
ALTER TABLE `sangria`
ADD CONSTRAINT `fk_sangria_estoque1` FOREIGN KEY (`nr_repasse`, `om_codom`) REFERENCES `estoque` (`nr_repasse`, `om_codom`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_sangria_usuario1` FOREIGN KEY (`usuario_idt`) REFERENCES `usuario` (`idt`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`om_codom`) REFERENCES `om` (`codom`) ON UPDATE CASCADE;

--
-- Constraints for table `viatura`
--
ALTER TABLE `viatura`
ADD CONSTRAINT `viatura_ibfk_1` FOREIGN KEY (`om_codom`) REFERENCES `om` (`codom`) ON UPDATE CASCADE,
ADD CONSTRAINT `viatura_ibfk_2` FOREIGN KEY (`combustivel_codigo`) REFERENCES `combustivel` (`codigo`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
